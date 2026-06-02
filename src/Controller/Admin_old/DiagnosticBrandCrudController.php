<?php

namespace App\Controller\Admin_old;

use App\Entity\DiagnosticBrand;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;

//use Symfony\Contracts\Cache\ItemInterface;
//use Symfony\Component\Cache\Adapter\AdapterInterface;

class DiagnosticBrandCrudController extends AbstractCrudController
{
    /*protected $cache;
    public function __construct(AdapterInterface $cache)
    {
        $this->cache = $cache;
    }*/

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
        //$article = AssociationField::new('article')->useResultCache(true, 3600, self::CACHE_KEY);
        $article = AssociationField::new('article')->setCrudController(ApcCache::class)->hideOnIndex();
        /*$article = $cache->get('article', function (ItemInterface $item) {
            $item->expiresAfter(3600);
            $computedValue = AssociationField::new('article');
        
            return $computedValue;
        });*/
        
        return [
            Field::new('id')->onlyOnIndex(),
            AssociationField::new('brand'),
            //$this->getCachedGroup(AssociationField::new('article')), // not worked
            //AssociationField::new('article')->setCrudController(ApcCache::class),
            //AssociationField::new('article')->setCrudController(ApcCache::class)->hideOnIndex(),
            $article,
            ImageField::new('image')->setUploadDir('/public/img/diagnostics/')->setBasePath('/img/diagnostics'),
        ];
    }

}
