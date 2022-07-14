<?php

namespace App\Controller\Admin_old;

use App\Entity\DiagnosticBrand;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;

class DiagnosticBrandCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DiagnosticBrand::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Элемент')
            ->setEntityLabelInPlural('Элементы');
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            Field::new('id')->onlyOnIndex(),
            AssociationField::new('brand'),
            AssociationField::new('article')->autocomplete(),
            ImageField::new('image')->setUploadDir('/public/img/diagnostics/')->setBasePath('/img/diagnostics'),
        ];
    }

}
