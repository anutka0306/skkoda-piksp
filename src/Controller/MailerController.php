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

        $url = $request->server->get('HTTP_REFERER');
        $address = 'СПБ4';

        /* send to telegram */
        $arr = array(
            "Имя" => $request->get('name'),
            "Телефон" => $request->get('phone'),
            "Сообщение" => $request->get('message'),
            "Со страницы" => $url,
        );

        $this->sendTelegram($arr, $address);


        $this->getRoistat($request->get('phone'), $url, $request->get('name') , '', '', $address);
        //End roistat


        return new JsonResponse(
            [
                'success' => true,
                'message' => 'Спасибо! Ваша заявка отправлена.'
            ]);
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

        $url = 'https://cloud.roistat.com/api/proxy/1.0/leads/add';

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url . '?' . http_build_query($roistatData),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_TIMEOUT => 3,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            error_log('Roistat error: '.curl_error($ch));
        }

        curl_close($ch);
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

        $token = "7357196560:AAHE-smtRk-geJmTKswjLhKglUzNp7ymexE";
        $chat_id = "-1001668945809"; # MERCEDES-PIK // Если "Другое" не "К20", "В2АЕ", "СПБ4"
        
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
}
