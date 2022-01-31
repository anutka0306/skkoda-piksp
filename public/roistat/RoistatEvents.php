<?php

require_once __DIR__ . '/NewRoistat.php';

class RoistatEvents {

    /**
     * Массив формы
     *
     * @var array
     */
    private $data;

    private $formName = 'Неизвестная форма';

    private const FORM_TYPE = [
        'tpl.about_form'            => 'Записаться на СТО',
        'tpl.questionsform'         => 'Свяжитесь с нами',
        'Получить расчет стоимости' => 'Получить расчет стоимости',
    ];

    private const FIELDS_NAME = [
        'marks_auto'        => 'Модель автомобиля',
        'years_auto'        => 'Год выпуска',
        'avto_na_hodu'      => 'Автомобиль на ходу?',
        'nuzhny_zapchasti'  => 'Вам требуются запчасти?',
        'text_problems'     => 'Какая у вас проблема?',
    ];

    public function __construct(array $data, string $formName = null)
    {
        $this->formName = $formName;
        $this->data = $data;
    }

    /**
     * Получить название формы
     *
     * @return string
     */
    private function getFormName() : string
    {
        return self::FORM_TYPE[$this->data['form-name']] ?? $this->formName;
    }

    /**
     * Получить все поля формы
     *
     * @return string
     */
    private function getComment() : string
    {
        $comment = '';
        foreach ($this->data as $key => $item) {
            if(array_key_exists($key, self::FIELDS_NAME)) {
                $comment .= self::FIELDS_NAME[$key] . ": {$item}\r\n";
            }
        }
        return $comment;
    }

    public function getCallbackPhone()
    {
        if(array_key_exists('roistat_phone_script_data', $_COOKIE)) {
            $callbackPhoneJson      = json_decode($_COOKIE['roistat_phone_script_data'], true);
            $currentCallbackPhone   = current($callbackPhoneJson); 
            return $currentCallbackPhone['phone'];
        }
        return null;
    }

    /**
     * Отправить в ройстат
     * @return false|mixed
     */
    public function execute()
    {
        return (new Roistat())
            ->setPhone($this->data['phone'])
            ->setForm( $this->getFormName() )
            ->setComment( $this->getComment() . "\r\nПодменный номер: " . $this->getCallbackPhone() )
            ->setIsNeedCallback(1)
            ->setCallBackPhone( $this->getCallbackPhone() )
            ->setFields([
                'form_name' => $this->getFormName()
            ])
            ->execute()
        ;
    }

}