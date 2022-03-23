<?php

namespace App\Form;

use App\Entity\Service;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CKEditorType extends AbstractType
{
   /* public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('brand_id')
            ->add('model_id')
            ->add('path')
            ->add('text')
            ->add('sort')
            ->add('images')
            ->add('published')
            ->add('text_down')
            ->add('text_down2')
            ->add('text_down_img')
            ->add('text_down_img2')
            ->add('text_down_bg')
            ->add('text_down3')
            ->add('text_down_img3')
            ->add('text_down4')
            ->add('text_down_img4')
            ->add('page_icon')
            ->add('text_img')
            ->add('adv_icon1')
            ->add('adv_icon2')
            ->add('adv_icon3')
            ->add('adv_icon4')
            ->add('adv_text1')
            ->add('adv_text2')
            ->add('adv_text3')
            ->add('adv_text4')
            ->add('modifyDate')
            ->add('name')
            ->add('h1')
            ->add('metaTitle')
            ->add('metaDescription')
            ->add('ratingValue')
            ->add('ratingCount')
            ->add('parent')
            ->add('service')
            ->add('price_category')
        ;
    }*/

    public  function getParent()
    {
        return CKEditorType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
