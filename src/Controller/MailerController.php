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

            //        $phone = '', $comment = '', $name = '', $email = ''
            $this->getRoistat(
                $userPhone,
                $request->get('salon_contact') . ' - ' . $request->get('comment_contact'),
                $userName,
                $userEmail

            );
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
     * @Route("/callback_form", name="callback_form")
     */
    public function callback_form(Request $request, MailerInterface $mailer)
    {
        if (!empty($request->get('b_control'))) {
            throw new BadRequestHttpException('Bot detected.');
        }
        // Форма "Заказать Звонок", "Записаться на СТО"
        /* send to telegram */
        $arr = array(
            "Заявка" => " с формы ".$request->get('form'),
            "Телефон" => $request->get('phone'),
            "Со страницы " => 'https://piksp.ru'.$request->get('url'),
            "Марка" => $request->get('mark'),
            "Адрес" => $request->get('address'),
        );
        /*$result['arr'] = $arr;
        $result['post'] = $post;
        echo json_encode($result, JSON_UNESCAPED_UNICODE);return;*/
        $this->sendTelegram($arr, $request->get('address'));

        //Begin roistat
        // $roistatFilePath = "{$_SERVER['DOCUMENT_ROOT']}/roistat/RoistatEvents.php";
        // if(is_file($roistatFilePath)) {
        //     require_once $roistatFilePath;
        //     try {
        //         $event = new \RoistatEvents($_REQUEST, 'Заявка с формы piksp.ru');
        //         $event->execute();
        //     }catch (\Exception $exception) {}
        // }
        $this->getRoistat($request->get('phone'), 'https://piksp.ru'.$request->get('url'), '' , '', $request->get('mark'), $request->get('address'));
        //End roistat

        $to = 'info@piksp.ru';

        $email = (new Email())
            ->from('info@my-side.ru')
            ->to((string)$to)
            ->subject('Новая заявка с сайта Piksp.ru')
            ->html('<p>Новая заявка с сайта Piksp.ru</p>
            <p>Телефон отправителя: ' . $request->get('phone') . '</p>'
            );
        //$mailer->send($email);


        return new JsonResponse(['success'=>'<p>Спасибо! Ваша заявка отправлена.</p>']);
    }


    /**
     * @Route("/raschet_form", name="raschet_form")
     */
    public function raschet_form(Request $request, MailerInterface $mailer)
    {

        //        $phone = '', $comment = '', $name = '', $email = ''
        $this->getRoistat(
            $request->get('phone'),
            'Марка = ' . $request->get('mark') .
            ' Год = ' . $request->get('year') .
            ' Эвакуатор = ' . $request->get('evakuator') .
            ' Запчасти = ' . $request->get('zapchasti') .
            ' Проблема = ' . $request->get('problem') .
            'URL = https://piksp.ru'.$request->get('url')
        );

        $token = "1737028189:AAEFd51Z6vSHslgX-CNMtItwWD6Iy5EIP74";
        $chat_id = "-1001408803296";# Заявки VAG-PIK

        $arr = array(
            "Заявка с" => " с формы запроса расчета piksp.ru ",
            "Телефон" => $request->get('phone'),
            "Марка авто" => $request->get('mark'),
            "Года авто" => $request->get('year'),
            "Нужен ли эвакуатор" => $request->get('evakuator'),
            "Нужны ли запчасти" => $request->get('zapchasti'),
            "Описание проблемы" => $request->get('problem'),
            "Со страницы: " => 'https://piksp.ru'.$request->get('url'),
        );
        /*Цикл по массиву (собираем сообщение) */
        $txt = '';
        foreach($arr as $key => $value) {
            $txt .= "<b>".$key."</b>: ".htmlspecialchars($value)."\n";
        }
        if (!$this->sendToTelegram($token, $chat_id, $txt)) {
            return new JsonResponse(['error' => '<p>Ошибка при отправке в Telegram</p>']);
        }

        /*$to = 'info@piksp.ru';

        $email = (new Email())
            ->from('info@my-side.online')
            ->to((string)$to)
            ->subject('Новая заявка с сайта Piksp.ru')
            ->html('<p>Новая заявка с сайта Piksp.ru</p>
            <p>Телефон отправителя: ' . $request->get('phone') . '</p>'
            );
        $mailer->send($email);*/


        return new JsonResponse(['success'=>'<p>Спасибо! Ваша заявка отправлена.</p>']);
    }

    public function addEmail($email, ValidatorInterface $validator)
    {
        $emailConstraint = array(
            new Assert\Email(),
            new Assert\NotBlank(),
        );
        $errors = $validator->validate(
            $email,
            $emailConstraint
        );

        if(0 === count($errors)){
            return true;
        }else{
            return false;
        }
    }

    public function addName($name, ValidatorInterface $validator)
    {
        $nameConstraint = array(
            new Assert\NotBlank(),
            new Assert\Length(['min' => 2]),
            new Assert\Regex(['pattern' => '/^[а-яёА-ЯЁ]+$/'])
        );

        $errors = $validator->validate(
            $name,
            $nameConstraint
        );
        if(0 === count($errors)){
            return true;
        }else{
            return false;
        }
    }

    public function addPhone($phone, ValidatorInterface $validator)
    {
        $phoneConstraint = array(
            new Assert\NotBlank(),
            new Assert\Regex(['pattern' => '/^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/'])
        );
        $errors = $validator->validate(
            $phone,
            $phoneConstraint
        );
        if(0 === count($errors)){
            return true;
        }else{
            return false;
        }
    }

    public function getTo($salon)
    {
        switch ($salon) {
            case 'Научный':
                return 'anya-programmist@qmotors.ru, webmaster@qmotors.ru, service@tokyogarage.ru, direktor@tokyogarage.ru, master@tokyogarage.ru,kostin@qmotors.ru';
            case 'Лобненская':
                return 'info@mirakpp.ru, maxima-x@yandex.ru, service@qmotors.ru, direktor@qmotors.ru, webmaster@qmotors.ru, w.ww@mail.ru,kostin@qmotors.ru,kostin@qmotors.ru';
            case 'Севастопольский':
                return 'webmaster@qmotors.ru,service@rovercity.ru,master@rovercity.ru,direktor@rovercity.ru,kostin@qmotors.ru';
            case 'Нижегородка':
                return 'webmaster@qmotors.ru,5service@qmotors.ru,5direktor@qmotors.ru,5master@qmotors.ru,kostin@qmotors.ru';
            case 'Удальцова':
                return '2direktor@qmotors.ru,2service@qmotors.ru,2master@qmotors.ru,webmaster@qmotors.ru,kostin@qmotors.ru';
            default:
                return 'anya-programmist@qmotors.ru, Sav@styled.cc';
        }
    }

    public function getTo_salonWithout()
    {
        return 'anya-programmist@qmotors.ru, Sav@styled.cc';
    }
    

    private function getRoistat($phone = '', $comment = '', $name = '', $email = '' , $mark = '', $addres = '')
    {
        $callbackPhone = '78129159153';
        if (array_key_exists('roistat_phone_script_data', $_COOKIE)) {
            $callbackPhoneJson      = json_decode($_COOKIE['roistat_phone_script_data'], true);
            $currentCallbackPhone   = current($callbackPhoneJson);
            
            $callback = str_replace([' ', '(', ')', '-', '&nbsp;'], '', $currentCallbackPhone['phone']);
            $callback = trim($callback);
            
            //if (isset($_GET['utm_source']) && $_GET['utm_source'] == 'ya-karti-lexus')
            $callbackPhone = preg_replace('/^\+?(8|7)/', '7', $callback);
        }
        
        $roistatData = array(
            'roistat' => isset($_COOKIE['roistat_visit']) ? $_COOKIE['roistat_visit'] : 'nocookie',
            'key'     => 'ODUxOTY2ZGIxZTAzOWRlNGU0M2IwYTBlOTgzNDczYzI6MTE2MDU4', // Ключ для интеграции с CRM, указывается в настройках интеграции с CRM.
            'title'   => 'Заявка с формы сайта Piksp.ru', // Название сделки
            'comment' => $comment . ' — +'.$callbackPhone, // Комментарий к сделке
            'name'    => $name, // Имя клиента
            'email'   => $email, // Email клиента
            'phone'   => $phone, // Номер телефона клиента
			//'order_creation_method' => '', // Способ создания сделки (необязательный параметр). Укажите то значение, которое затем должно отображаться в аналитике в группировке "Способ создания заявки"
            'is_need_callback' => '1', // Если указано значение '1', на номер клиента будет инициироваться обратный звонок после создания заявки в Roistat (независимо от того, включен ли обратный звонок в Ловце лидов). Если указано значение '0', для данной формы обратный звонок инициироваться не будет (даже если в Ловце лидов включен обратный звонок).
            'callback_phone' => $callbackPhone, // Переопределяет номер, указанный в настройках обратного звонка.
            'sync'    => '1', // Было 0 - то есть была отключена
			//'is_need_check_order_in_processing' => '1', // Включение проверки заявок на дубли
			//'is_need_check_order_in_processing_append' => '1', // Если создана дублирующая заявка, в нее будет добавлен комментарий об этом
			//'is_skip_sending' => '1', // Не отправлять заявку в CRM.
            'fields'  => array(
                'Адрес'   => $addres,
                'Марка'   => $mark,
                'Модель'  => '',
                'Сайт'    => 'Piksp.ru',
				//'charset'    => 'Windows-1251' // Сервер преобразует значения полей из указанной кодировки в UTF-8.
            ),
        );
/*
		//В2АЕ				250				251/252/254
		//К20 Land Rover	231				231
		if ($mark == 'Land Rover' || $addres == 'К20') {
			$manager = '231';
		} else {
            if (date("H") % 2 === 0) {
                $manager = (rand(1, 2) == 1) ? '251' : '254';
            } else {
                $manager = (rand(1, 2) == 1) ? '252' : '254';
            }
		}

		$this->megaCall($phone, $manager, $callbackPhone);
		*/
		
        /* send to megafon */
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
		
        file_get_contents("https://cloud.roistat.com/api/proxy/1.0/leads/add?" . http_build_query($roistatData));
    }
	
    public function megaCall($user, $manager, $clid = '') {
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
        // $token = "7357196560:AAHE-smtRk-geJmTKswjLhKglUzNp7ymexE";
        // $chat_id = "-1001668945809";
        // $token = "1737028189:AAEFd51Z6vSHslgX-CNMtItwWD6Iy5EIP74";
        // $chat_id = "-1001493902889"; // Заявки VAG-PIK
        // $chat_id = "-1001408803296"; // ПИКСПБ Лексус
        // $token = "2102312578:AAF6iR_1pAUR4GY1Vg8TwgF3CsIBCKWQyBg";
        // $chat_id = "-1001677654724"; // Заявки VAG-PIK
        $token = "7357196560:AAHE-smtRk-geJmTKswjLhKglUzNp7ymexE";
        $chat_id = "-1001668945809"; # MERCEDES-PIK // Если "Другое" не "К20", "В2АЕ", "СПБ4"
        
        if ($address == 'СПБ4') {
            $token = "7357196560:AAHE-smtRk-geJmTKswjLhKglUzNp7ymexE";
            $chat_id = '-1001707616285'; // Пик СПБ4 Заявки
        } 
        if ($address == 'К20') {
            // $token = "1737028189:AAEFd51Z6vSHslgX-CNMtItwWD6Iy5EIP74";
            // $chat_id = "-1001493902889"; // Заявки VAG-PIK
        	$token = "7357196560:AAHE-smtRk-geJmTKswjLhKglUzNp7ymexE";
        	$chat_id = '-1001616535220'; // Пик К20 Vag Заявки
        }
        if ($address == 'В2АЕ') {
            $token = "1737028189:AAEFd51Z6vSHslgX-CNMtItwWD6Iy5EIP74";
            $chat_id = "-1001408803296"; // ПИКСПБ Лексус
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
}
