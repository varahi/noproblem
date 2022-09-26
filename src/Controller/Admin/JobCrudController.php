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
        yield IntegerField::new('id')->setFormTypeOption('disabled', 'disabled');
        yield BooleanField::new('hidden');
        yield TextField::new('name');
        yield TextareaField::new('description');
        yield ImageField::new('image')
            ->setBasePath('uploads/')
            ->setUploadDir('public_html/uploads')
            ->setFormType(FileUploadType::class)
            ->setRequired(false);
        yield TextField::new('age');
        yield ChoiceField::new('experience')->setChoices(
            [
                'Нет' => null,
                'Менее 6 месяцев' => '1',
                '6-12 месяцев' => '2',
                '2-5 лет' => '3',
                'более 5 лет' => '4',
            ]
        )->hideOnIndex();
        yield ChoiceField::new('education')->setChoices(
            [
                'Нет' => null,
                'Начальное' => '1',
                'Среднее' => '2',
                'Техническое' => '3',
                'Неполное высшее' => '4',
                'Высшее' => '5',
            ]
        )->hideOnIndex();
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
        )->hideOnIndex();
        yield BooleanField::new('startNow')->hideOnIndex();
        yield DateField::new('startDate')->hideOnIndex();
        yield AssociationField::new('city')->hideOnIndex();
        yield AssociationField::new('category')->hideOnIndex();
        yield AssociationField::new('client');
    }
}
