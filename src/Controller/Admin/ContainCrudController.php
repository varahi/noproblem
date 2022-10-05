<?php

namespace App\Controller\Admin;

use App\Entity\Contain;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;

class ContainCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contain::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Contain')
            ->setEntityLabelInPlural('Contain')
            ->setSearchFields(['name', 'teaser'])
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id')->setFormTypeOption('disabled', 'disabled');
        yield BooleanField::new('hidden');
        yield TextField::new('name');
        yield TextareaField::new('teaser');
        yield AssociationField::new('course');
        yield ChoiceField::new('type')->setChoices(
            [
                'Тип *' => null,
                'Текстовый' => '1',
                'Видео' => '2',
                'Картинка' => '3',
            ]
        )->hideOnIndex();
    }
}
