<?php

namespace App\Controller\Admin;

use App\Entity\Course;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class CourseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Course::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Course')
            ->setEntityLabelInPlural('Course')
            ->setSearchFields(['name'])
            ->setDefaultSort(['id' => 'DESC'])
            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id')->setFormTypeOption('disabled', 'disabled');
        yield BooleanField::new('hidden');
        yield TextField::new('name');
        yield SlugField::new('slug')->hideOnIndex()->setTargetFieldName('name');
        yield TextareaField::new('teaser');
        yield AssociationField::new('contain');
        yield ImageField::new('image')
            ->setBasePath('uploads/')
            ->setUploadDir('public_html/uploads')
            ->setFormType(FileUploadType::class)
            ->setRequired(false);
        yield TextEditorField::new('bodytext')->hideOnIndex()->setFormType(CKEditorType::class);
        yield MoneyField::new('price')->setCurrency('RUB')->setCustomOption('storedAsCents', false);
        yield MoneyField::new('discountedPrice')->setCurrency('RUB')->setCustomOption('storedAsCents', false);
    }

    /*public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('articles'))
            ;
    }*/

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
