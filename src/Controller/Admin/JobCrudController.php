<?php

namespace App\Controller\Admin;

use App\Entity\Job;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;

class JobCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Job::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Job')
            ->setEntityLabelInPlural('Job')
            ->setSearchFields(['name'])
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('category'))
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel('Main info')->setIcon('fa fa-info')->setCssClass('col-sm-8');
        //yield FormField::addRow();

        yield IntegerField::new('id')->setFormTypeOption('disabled', 'disabled')->hideWhenCreating();
        yield BooleanField::new('hidden');
        yield TextField::new('name')->setColumns('col-md-8');
        yield TextareaField::new('description')->setColumns('col-md-8');
        yield ImageField::new('image')
            ->setBasePath('uploads/')
            ->setUploadDir('public_html/uploads')
            ->setFormType(FileUploadType::class)
            ->setRequired(false);

        yield FormField::addPanel('Additional info')->setIcon('fa fa-info-circle')->setCssClass('col-sm-4');
        //yield FormField::addRow();
        yield AssociationField::new('city')->hideOnIndex()->setColumns('col-md-12');
        yield AssociationField::new('category')->hideOnIndex()->setColumns('col-md-12');
        yield AssociationField::new('client')->setColumns('col-md-12');

        yield FormField::addPanel('General requirements')->setIcon('fa fa-gear');
        yield FormField::addRow();
        yield TextField::new('age')->setColumns('col-md-4');
        yield ChoiceField::new('experience')->setChoices(
            [
                'Нет' => null,
                'Менее 6 месяцев' => '1',
                '6-12 месяцев' => '2',
                '2-5 лет' => '3',
                'более 5 лет' => '4',
            ]
        )
            ->hideOnIndex()
            ->setColumns('col-md-4');
        yield ChoiceField::new('education')->setChoices(
            [
                'Нет' => null,
                'Начальное' => '1',
                'Среднее' => '2',
                'Техническое' => '3',
                'Неполное высшее' => '4',
                'Высшее' => '5',
            ]
        )
            ->hideOnIndex()
            ->setColumns('col-md-4');

        yield FormField::addRow();
        yield ChoiceField::new('citizen')->setChoices(
            [
                'Нет' => null,
                'РФ' => '1',
                'Белоруссия' => '2',
                'Армения' => '3',
                'Азербайджан' => '4',
                'Грузия' => '5',
                'Казахстан' => '6',
                'Киргизия' => '7',
                'Молодова' => '8',
                'Таджикистан' => '9',
                'Узбекистан' => '10',
                'Страны ЕС' => '11',
            ]
        )
            ->hideOnIndex()
            ->setColumns('col-md-4');

        yield BooleanField::new('startNow')->hideOnIndex();
        yield DateField::new('startDate')->hideOnIndex();
    }
}
