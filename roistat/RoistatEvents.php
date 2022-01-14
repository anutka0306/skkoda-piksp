<?php

require_once __DIR__ . '/NewRoistat.php';

class RoistatEvents {

    /**
     * Массив формы
     *
     * @var array
     */
    private $data;

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

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Получить название формы
     *
     * @return string
     */
    private function getFormName() : string
    {
        return self::FORM_TYPE[$this->data['form-name']] ?? 'Неизвестная форма';
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

    /**
     * Отправить в ройстат
     * @return false|mixed
     */
    public function execute()
    {
        return (new Roistat())
            ->setPhone($this->data['phone'])
            ->setForm( $this->getFormName() )
            ->setComment( $this->getComment() )
            ->setIsNeedCallback(1)
            ->setCallBackPhone(
                array_key_exists('roistat_phone', $_COOKIE) ? $_COOKIE['roistat_phone'] : null
            )
            ->execute()
        ;
    }

}