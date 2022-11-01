<?php

namespace App\Controller\Admin;

use App\Entity\Job;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
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
            ->setSearchFields(['name', 'description', 'age', 'address'])
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('category'))
            ->add(EntityFilter::new('client'))
            ->add(EntityFilter::new('owner'))
            ->add(EntityFilter::new('city'))
            ->add(EntityFilter::new('district'))
            ->add(EntityFilter::new('experience'))
            ->add(EntityFilter::new('education'))
            ->add(EntityFilter::new('citizen'))
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel('Main info')->setIcon('fa fa-info')->setCssClass('col-sm-8');
        //yield FormField::addRow();

        yield IntegerField::new('id')->setFormTypeOption('disabled', 'disabled')->hideWhenCreating();
        yield DateTimeField::new('created')->setFormTypeOption('disabled', 'disabled');
        yield BooleanField::new('hidden');
        yield TextField::new('name')->setColumns('col-md-8');
        yield TextField::new('contactFullName')->setColumns('col-md-8')->hideOnIndex();
        yield TextareaField::new('description')->setColumns('col-md-8')->hideOnIndex();
        yield TextField::new('payment')->setColumns('col-md-8');
        yield ImageField::new('image')
            ->setBasePath('uploads/files/')
            ->setUploadDir('public_html/uploads/files')
            ->setFormType(FileUploadType::class)
            ->setRequired(false);

        yield TextareaField::new('schedule')->setColumns('col-md-8')->hideOnIndex()->setFormTypeOption('disabled', 'disabled');

        yield FormField::addPanel('Additional info')->setIcon('fa fa-info-circle')->setCssClass('col-sm-4');
        //yield FormField::addRow();
        yield AssociationField::new('city')->hideOnIndex()->setColumns('col-md-12');
        yield AssociationField::new('district')->hideOnIndex()->setColumns('col-md-12');
        yield AssociationField::new('category')->hideOnIndex()->setColumns('col-md-12');
        yield AssociationField::new('client')->setColumns('col-md-12')->setLabel('Job performer')->hideOnIndex();
        yield AssociationField::new('owner')->setColumns('col-md-12')->setLabel('Job owner')->hideOnIndex();
        yield AssociationField::new('tasks')->setColumns('col-md-12')->setLabel('Tasks')->hideOnIndex();
        yield AssociationField::new('additional')->setColumns('col-md-12')->setLabel('Additional info')->hideOnIndex();

        yield AssociationField::new('accommodations')
            ->setFormTypeOptions([
                'by_reference' => false,
            ])->hideOnIndex()->setColumns('col-md-12')->setLabel('Accommodation')->hideOnIndex();

        yield AssociationField::new('busynesses')
            ->setFormTypeOptions([
                'by_reference' => false,
            ])->hideOnIndex()->setColumns('col-md-12')->setLabel('Busynnes')->hideOnIndex();

        yield AssociationField::new('featuredUsers')
            ->setFormTypeOptions([
                'by_reference' => false,
            ])->hideOnIndex()->setColumns('col-md-12')->setLabel('Featured Users')->hideOnIndex();

        yield FormField::addPanel('General requirements')->setIcon('fa fa-gear');
        yield FormField::addRow();
        yield TextField::new('age')->setColumns('col-md-4');
        yield AssociationField::new('experience')->setColumns('col-md-4')->hideOnIndex();
        yield AssociationField::new('education')->setColumns('col-md-4')->hideOnIndex();

        yield FormField::addRow();
        yield AssociationField::new('citizen')->setColumns('col-md-4')->hideOnIndex();
        yield BooleanField::new('startNow')->hideOnIndex();
        yield DateField::new('startDate')->hideOnIndex();

        yield FormField::addPanel('Geo')->setIcon('fa fa-map-marker');
        yield FormField::addRow();
        yield NumberField::new('latitude')->hideOnIndex()->setFormTypeOption('scale', 8)->setColumns('col-md-4');
        yield NumberField::new('longitude')->hideOnIndex()->setFormTypeOption('scale', 8)->setColumns('col-md-4');
    }
}
