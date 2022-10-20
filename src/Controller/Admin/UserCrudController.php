<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AvatarField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Doctrine\ORM\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): \Doctrine\ORM\QueryBuilder
    {
        $role = 'ROLE_SUPER_ADMIN';
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $qb->where('entity.roles LIKE :roles');
        $qb->setParameter('roles', '%"'.$role.'"%');

        return $qb;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('User')
            ->setEntityLabelInPlural('User')
            ->setSearchFields(['firstName', 'lastName', 'email'])
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id')->setFormTypeOption('disabled', 'disabled');
        yield EmailField::new('email');
        yield TextField::new('firstName');
        yield TextField::new('lastName');
        //yield TextField::new('email');
        //yield TextField::new('phone');
        yield ArrayField::new('roles')->hideOnIndex()->setFormTypeOption('disabled', 'disabled');
//        yield ImageField::new('avatar')
//            ->setBasePath('/uploads/files')
//            ->setLabel('Avatar')
//            ->onlyOnIndex();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            //->disable('new', 'edit', 'delete');
            ->disable('new', 'delete');
    }
}
