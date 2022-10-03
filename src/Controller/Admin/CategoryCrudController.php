<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Categories')
            ->setEntityLabelInPlural('Categories')
            ->setSearchFields(['name', 'description'])
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('jobs'))
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel('Main info')->setIcon('fa fa-info')->setCssClass('col-sm-8');
        yield IntegerField::new('id')->setFormTypeOption('disabled', 'disabled')->hideWhenCreating();
        yield TextField::new('name')->setColumns('col-md-8');
        yield TextareaField::new('description')->setColumns('col-md-10');

        yield FormField::addPanel('Relations')->setIcon('fa fa-chain')->setCssClass('col-sm-4');
        yield BooleanField::new('isHidden');
        yield ImageField::new('image')
            ->setBasePath('uploads/')
            ->setUploadDir('public_html/uploads')
            ->setFormType(FileUploadType::class)
            ->setRequired(false)
            ->setColumns('col-md-12');
        yield AssociationField::new('jobs')
            ->setFormTypeOptions([
                'by_reference' => false,
            ])->hideOnIndex()
            ->setColumns('col-md-12');
        //yield AssociationField::new('users');
    }
}
