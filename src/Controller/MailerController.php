<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Mime\Message;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class MailerController extends AbstractController
{
    private $validatorInterface;

    public function __construct(ValidatorInterface $validatorInterface)
    {
        $this->validatorInterface = $validatorInterface;
    }

    /**
     * @Route("/contact_form", name="contact_form")
     */
    public function contact_form(Request $request, MailerInterface $mailer)
    {
        // Форма "Свяжитесь с нами"
        if (!empty($request->get('b_control'))) {
            throw new BadRequestHttpException('Bot detected.');
        }
        
        /* send to telegram */
        $arr = array(
            "Заявка" => " с формы ".$request->get('form'),
            "Телефон" => $request->get('phone'),
            "Со страницы " => 'https://piksp.ru'.$request->get('url'),
            "Марка" => $request->get('mark'),
            "Адрес" => $request->get('address'),
        );
        
        $this->sendTelegram($arr, $request->get('address'));
        
        $to = explode(',',$this->getTo($request->get('salon')) );
        $errors = array();
        $userName ='';
        $userEmail = '';
        $userPhone = '';
        if(!$this->addEmail($request->get('user_email_contact'), $this->validatorInterface)){
            $errors[] = 'Некорректный E-mail адрес';
        }
        if (!$this->addName($request->get('user_name_contact'), $this->validatorInterface)){
            $errors[] = 'Имя должно содержать не меньше 2-х символов. Может содержать только русские буквы.';
        }
        if (!$this->addPhone($request->get('user_phone_contact'), $this->validatorInterface)) {
            $errors[] = 'Некорректный номер телефона';
        } else{
            $userEmail = $request->get('user_email_contact');
            $userName = $request->get('user_name_contact');
            $userPhone = $request->get('user_phone_contact');

            $this->callManager($userPhone);
        }
        
        if (0 === count($errors)) {
            foreach ($to as $recipient){
                $email = (new Email())
                    ->from('robot@mirakpp.ru')
                    ->to($recipient)
                    ->subject('Новое сообщение с сайта Sav@styled.cc')
                    ->html('<p>Сообщение со страницы контакты:</p>
                     <p>Имя отправителя: ' . $userName . '</p>
                    <p>E-mail отправителя: ' . $userEmail . '</p>
                    <p>Телефон отправителя: ' . $userPhone . '</p>
                    <p>Салон: ' . $request->get('salon_contact') . '</p>
                    <p>Сообщение: ' . $request->get('comment_contact') . '</p>'
                    );
                //$mailer->send($email);
            }
            return new JsonResponse(['success'=>'<p>Спасибо! Ваше сообщение отправлено.</p>']);
        } else {
            return new JsonResponse(['errors'=>$errors]);
        }

    }


    /**
     * @Route("/vakancy_form", name="vakancy_form")
     */
    public function vakancy_form(Request $request, MailerInterface $mailer)
    {
        $errors = array();
        $userName ='';
        $userEmail = '';
        $userPhone = '';
        if(!$this->addEmail($request->get('user_email_contact'), $this->validatorInterface)){
            $errors[] = 'Некорректный E-mail адрес';
        }
        if (!$this->addName($request->get('user_name_contact'), $this->validatorInterface)){
            $errors[] = 'Имя должно содержать не меньше 2-х символов. Может содержать только русские буквы.';
        }
        if(!$this->addPhone($request->get('user_phone_contact'), $this->validatorInterface)){
            $errors[] = 'Некорректный номер телефона';
        }
        else{
            $userEmail = $request->get('user_email_contact');
            $userName = $request->get('user_name_contact');
            $userPhone = $request->get('user_phone_contact');

        }
        if(0 === count($errors)) {
            $email = (new Email())
                ->from('robot@mirakpp.ru') //otklik@qmotors.ru
                ->to('2hr@qmotors.ru')   //2hr@qmotors.ru
                ->subject('Отклик на вакансию с сайта mirakpp.ru')
                ->html('<p>Отклик на вакансию:</p>
             <p>Имя отправителя: ' . $userName . '</p>
<p>E-mail отправителя: ' . $userEmail . '</p>
<p>Телефон отправителя: ' . $userPhone . '</p>
<p>Салон: ' . $request->get('salon_contact') . '</p>
<p>Сообщение: ' . $request->get('comment_contact') . '</p>'
                );
            $mailer->send($email);

            return new JsonResponse(['success'=>'<p>Спасибо! Ваше сообщение отправлено.</p>']);
        }else{
            return new JsonResponse(['errors'=>$errors]);
        }

    }

    /**
     * @Route("/callback_form", name="callback_form", methods={"POST"})
     *
     * methods={"POST"} добавлен 27.08.2026: без него заявку и звонок с нашей АТС
     * на произвольный номер можно было инициировать обычной GET-ссылкой.
     *
     * Заявка уходит по трём каналам сети (настроено 27.08.2026):
     *   1. старый чат филиала — прямой sendMessage;
     *   2. Roistat — сквозная аналитика, проект 116058;
     *   3. новый чат — бот pikautobot, эндпоинт /site-lead.
     * Каналы независимы: падение любого не мешает остальным и не ломает форму клиенту.
     */
    public function callback_form(Request $request, MailerInterface $mailer)
    {
        if (!empty($request->get('b_control'))) {
            throw new BadRequestHttpException('Bot detected.');
        }

        // Страницу берём из поля url — его добавляет ajaxform/js/default.js;
        // HTTP_REFERER остаётся запасным вариантом.
        $url  = $request->get('url') ?: $request->server->get('HTTP_REFERER');
        $host = $request->getSchemeAndHttpHost();

        // Обе формы (v3/blocks/section/steps и v3/popup/popup_order) шлют адрес
        // человекочитаемой строкой, а чатам, Roistat и боту нужны разные коды филиала.
        // До 27.08.2026 адрес игнорировался: все заявки уходили в чат СПБ4.
        $branch = $this->resolveBranch($request->get('address'));

        /* 1. Старый чат филиала */
        $arr = array(
            "Заявка" => " с формы сайта " . $host . "/",
            "Имя" => $request->get('name'),
            "Телефон" => $request->get('phone'),
            // В формах вместо поля «Комментарий» теперь выбор адреса сервиса
            // (замечание заказчика 15.08.2026). Телефон менеджера по-прежнему один.
            "Адрес сервиса" => $branch['tg'],
            "Сообщение" => $request->get('message'),
            "Со страницы" => $url,
        );

        $txt = '';
        foreach ($arr as $key => $value) {
            $txt .= "<b>" . $key . "</b>: " . htmlspecialchars((string)$value) . "\n";
        }

        $this->sendTelegram($arr, $branch['ats']);

        /* 2. Roistat */
        try {
            $this->sendRoistat($request, $branch, $host, $url);
        } catch (\Throwable $e) { /* аналитика не должна ронять форму */ }

        /* 3. Новый чат @pikautobot */
        try {
            $this->notifyCallcenter(
                (string)$request->get('phone'),
                (string)$request->get('name'),
                $branch['code'],
                'skoda',
                $txt
            );
        } catch (\Throwable $e) { /* бот дополнителен, форму клиента не валим */ }

        // Автозвонок клиенту через Мегафон отключён 27.08.2026 по решению Владислава —
        // тем же решением, что 14.07.2026 сняло автозвонок на 43 файлах остальных
        // сайтов сети. Клиенту перезванивает менеджер из чата, а не АТС.
        // $this->callManager($request->get('phone'), 'СПБ4');


        return new JsonResponse(
            [
                'success' => true,
                'message' => 'Спасибо! Ваша заявка отправлена.'
            ]);
    }



    

    /**
     * Соединяет клиента с менеджером через АТС по адресу сервиса.
     * Интеграция с Roistat удалена 07.08.2026 — заказчик подключит свой проект.
     */
    private function callManager($phone = '', $addres = '')
    {
        $manager = '212';
        $clid = '78129130538';
        if ($addres == 'К20') {
            $clid = '78129130538';
            if (date("H") % 2 === 0) {
                $manager = (rand(1, 2) == 1) ? '212' : '214';
            } else {
                $manager = '213';
            }
        }
        if ($addres == 'Б116') {
            $clid = '78129998553';
            if (date("H") % 2 === 0) {
                $manager = (rand(1, 2) == 1) ? '312' : '314';
            } else {
                $manager = (rand(1, 2) == 1) ? '311' : '313';
            }
        }
        if ($addres == 'СПБ4') {
            $clid = '78129089556';
            $manager = '307';
        }
        $this->megaCall($phone, $manager, $clid);
    }

    public function megaCall($user, $manager, $clid = '') {
        // Автозвонок отключён по всей сети (14.07.2026), на этом сайте — 27.08.2026.
        // Заглушка стоит здесь, а не только в местах вызова, чтобы ни один путь
        // (в т.ч. contact_form) не поднимал звонок с корпоративной АТС.
        // Включается снятием одной строки.
        return;

        if (strtotime('9:00:00') < time() && strtotime('20:50:00') > time()) {
            $ch = curl_init(); 
            curl_setopt($ch, CURLOPT_URL, 'https://vats332138.megapbx.ru/crmapi/v1/makecall');
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['cmd: makeCall', 'X-API-KEY: 9afaf8e5-87cf-41b4-b8d9-0780038df43c']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'phone' => $user,       // client - Номер клиента, на который последует звонок - обязательно.
                'user'  => $manager,    // login or ext - Сотрудник, который будет соединен с клиентом. Допускается логин или короткий номер - обязательно.
                'clid'  => $clid        // Исходящий номер для звонка - необязательно.
            ]);
            $output = curl_exec($ch);
            curl_close($ch);
        }
    }

    /* Telegram */
    public function sendTelegram($arr, $address = '') {

        $token = "7357196560:AAHE-smtRk-geJmTKswjLhKglUzNp7ymexE";
        $chat_id = "-1001668945809"; # MERCEDES-PIK // Если "Другое" не "К20", "В2АЕ", "СПБ4"
        
        if ($address == 'В2АЕ') {
            // Площадка Ворошилова 2АЕ — тот же бот сети, другой чат (как на skoda.piksp.ru).
            $chat_id = '-1001408803296'; // ПИКСПБ Лексус
        }

        if ($address == 'СПБ4') {
            $token = "7357196560:AAHE-smtRk-geJmTKswjLhKglUzNp7ymexE";
            $chat_id = '-1001707616285'; // Пик СПБ4 Заявки
        } 


        $txt = '';
        foreach($arr as $key => $value) {
            $txt .= "<b>".$key."</b>: ".htmlspecialchars($value)."\n";
        }

        if (!$this->sendToTelegram($token, $chat_id, $txt)) {
            return new JsonResponse(['error' => '<p>Ошибка при отправке в Telegram</p>']);
        }

        return new JsonResponse(['success'=>'<p>Спасибо! Ваша заявка отправлена.</p>']);
    }

    private function sendToTelegram(string $token, string $chatId, string $text): bool
    {
        $url = "https://api.telegram.org/bot{$token}/sendMessage";

        $data = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML'
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_TIMEOUT => 10,
        ]);

        $result = curl_exec($ch);

        if ($result === false) {
            curl_close($ch);
            return false;
        }

        $response = json_decode($result, true);
        curl_close($ch);

        return isset($response['ok']) && $response['ok'] === true;
    }

    /**
     * Приводит адрес из формы к кодам сети ПИК.
     * code — для бота pikautobot, tg — текст для чата, roistat — поле «Адрес» в аналитике,
     * ats — код филиала для sendTelegram().
     */
    private function resolveBranch($raw)
    {
        $s = (string)$raw;

        if ($s !== '' && (mb_stripos($s, 'Турухтанные', 0, 'UTF-8') !== false
            || mb_stripos($s, 'ТО12', 0, 'UTF-8') !== false
            || mb_stripos($s, 'СПБ4', 0, 'UTF-8') !== false)) {
            return array(
                'code'    => 'to12',
                'tg'      => 'Дор. на Турухтанные Острова 12',
                'roistat' => 'СПБ4',
                'ats'     => 'СПБ4',
            );
        }

        if ($s !== '' && (mb_stripos($s, 'Ворошилова', 0, 'UTF-8') !== false
            || mb_stripos($s, 'В2АЕ', 0, 'UTF-8') !== false)) {
            return array(
                'code'    => 'v2ae',
                'tg'      => 'ул. Ворошилова д. 2АЕ',
                'roistat' => 'В2АЕ',
                'ats'     => 'В2АЕ',
            );
        }

        // Адрес не выбран или незнакомый — маршрут, который был на сайте до 27.08.2026.
        return array(
            'code'    => '',
            'tg'      => $s !== '' ? $s : 'не выбран',
            'roistat' => '',
            'ats'     => 'СПБ4',
        );
    }

    /**
     * Лид в Roistat (проект 116058) — тот же приём, что на остальных сайтах сети.
     * roistat_visit берётся из куки счётчика; без куки Roistat принимает заявку как 'nocookie'.
     */
    private function sendRoistat(Request $request, array $branch, $host, $url)
    {
        $roistatData = array(
            'roistat' => isset($_COOKIE['roistat_visit']) ? $_COOKIE['roistat_visit'] : 'nocookie',
            'key'     => 'MjcxMzIwOjExNjA1ODo2OWY4YmE5ZmQxMzUwZDY5NmFjYWNlNGJmNTAxNGExYQ==',
            'title'   => 'Заявка с формы сайта ' . $host . '/',
            'comment' => (string)$url,
            'name'    => (string)$request->get('name'),
            'email'   => '',
            'phone'   => (string)$request->get('phone'),
            'order_creation_method' => '',
            // Автозвонок Roistat выключен — по сети обратный звонок отключён 14.07.2026.
            'is_need_callback' => '0',
            'sync'    => '0',
            'is_need_check_order_in_processing' => '0',
            'is_need_check_order_in_processing_append' => '0',
            'is_skip_sending' => '0',
            'fields'  => array(
                'Адрес'  => $branch['roistat'],
                'Марка'  => 'Skoda',
                'Модель' => '-',
                'Сайт'   => preg_replace('~^https?://~', '', (string)$host),
            ),
        );

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL            => 'https://cloud.roistat.com/api/proxy/1.0/leads/add?' . http_build_query($roistatData),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT        => 7,
        ));
        curl_exec($ch);
        curl_close($ch);
    }

    /**
     * «Новые» чаты сети: заявка уходит боту pikautobot, он заводит тему на площадке.
     * Собака перед именем бота в докблоках запрещена: Doctrine читает любой @токен
     * как аннотацию и роняет весь роутинг (проверено на боевом 27.08.2026).
     * Врезка стандартная для сайтов сети, перенесена с боевого skoda.piksp.ru
     * (Режим A: бот дополняет старый чат, а не заменяет его).
     */
    private function notifyCallcenter($phone, $name, $addrCode, $brand, $comment) {
        try {
            if (!function_exists('curl_init')) { return; }
            $secret = getenv('SITE_LEAD_SECRET') ?: '64f240680d8ec71670ff408779e6370945d8f6362353e5ad';
            $payload = http_build_query(array(
                'phone'         => (string)$phone,
                'name'          => (string)$name,
                'address'       => (string)$addrCode,
                'brand'         => (string)$brand,
                'city'          => 'spb',
                'comment'       => (string)$comment,
                'roistat_visit' => isset($_COOKIE['roistat_visit']) ? $_COOKIE['roistat_visit'] : '',
            ));
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL               => 'https://hook.pikms.ru/site-lead',
                CURLOPT_POST              => true,
                CURLOPT_RETURNTRANSFER    => true,
                CURLOPT_HTTPHEADER        => array('X-Site-Secret: ' . $secret),
                CURLOPT_POSTFIELDS        => $payload,
                CURLOPT_NOSIGNAL          => true,
                CURLOPT_CONNECTTIMEOUT_MS => 1000,
                CURLOPT_TIMEOUT_MS        => 2500,
            ));
            curl_exec($ch);
            curl_close($ch);
        } catch (\Throwable $e) { /* Режим A: бот дополнителен, форму клиента не валим */ }
    }
}
