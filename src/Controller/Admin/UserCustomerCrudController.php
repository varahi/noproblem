<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
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
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserCustomerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): \Doctrine\ORM\QueryBuilder
    {
        $role = 'ROLE_CUSTOMER';
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
        yield BooleanField::new('hidden');
        yield TextField::new('firstName');
        yield TextField::new('lastName');
        yield TextField::new('email');
        yield TextField::new('username')->hideOnIndex();
        yield TextField::new('password')->setFormType(PasswordType::class)->hideOnIndex();
        yield TelephoneField::new('phone')->hideOnIndex();
        yield EmailField::new('email');
        yield ArrayField::new('roles')->hideOnIndex()->setFormTypeOption('disabled', 'disabled');
        yield ImageField::new('avatar')
            ->setBasePath('uploads/')
            ->setUploadDir('public_html/uploads')
            ->setFormType(FileUploadType::class)
            ->setRequired(false);
        yield TextareaField::new('about');
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
        yield TextareaField::new('address')->hideOnIndex();
        yield AssociationField::new('city')->hideOnIndex();
        yield AssociationField::new('category')->hideOnIndex();
        yield AssociationField::new('tariff')->hideOnIndex();
    }
}
