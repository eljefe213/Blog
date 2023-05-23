<?php

namespace App\Controller\Admin;

use App\Entity\Menu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\SubMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\RequestStack;

class MenuCrudController extends AbstractCrudController
{
    const MENU_PAGES = 0;
    const MENU_ARTICLES = 1;
    const MENU_LINKS = 2;
    const MENU_CATEGORIES = 3;

    public function __construct(
        private RequestStack $requestStack
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Menu::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        $subMenuIndex = $this->getSubMenuIndex();
        //return parent::configureCrud($crud);
        $entityLabelInSingular = 'un menu';

        $entityLabelInPlural = match ($subMenuIndex) {
            self::MENU_ARTICLES => 'Articles',
            self::MENU_CATEGORIES => 'Catégories',
            self::MENU_LINKS => 'Liens personnalisés',
            default => 'Pages'
        };
        return $crud
            ->setEntityLabelInSingular($entityLabelInSingular)
            ->setEntityLabelInPlural($entityLabelInPlural);
    }


    public function configureFields(string $pageName): iterable
    {
        $subMenuIndex = $this->getSubMenuIndex();

        yield IdField::new('name', 'Titre de la navigation');

        yield NumberField::new('menuOrder', 'Ordre');

        yield $this->getFieldFromSubMenuIndex($subMenuIndex)
            ->setRequired(true);

        yield BooleanField::new('isVisible', 'Visible');

        yield AssociationField::new('subMenus', 'Sous-élèments');
    }

    private function getFieldFromSubMenuIndex(int $subMenuIndex)
    {
        $fieldName = match ($subMenuIndex) {
            self::MENU_ARTICLES => 'article',
            self::MENU_CATEGORIES => 'Category',
            self::MENU_LINKS => 'link',
            default => 'page'
        };
        return ($fieldName == 'link') ? TextField::new($fieldName, 'lien') : AssociationField::new($fieldName);
    }



    private function getSubMenuIndex(): int
    {
        $query = $this->requestStack->getMainRequest()->query;

        if ($referer = $query->get('referrer')) {
            parse_str(parse_url($referer, PHP_URL_QUERY), $query);

            return $query['submenuIndex'] ?? 0;
        }

        return $query->getInt('submenuIndex');
        // return $this->requestStack->getMainRequest()->query->getInt('submenuIndex');
    }
}
