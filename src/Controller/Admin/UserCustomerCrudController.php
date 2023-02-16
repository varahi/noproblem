<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
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
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

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
            ->setEntityLabelInSingular('Customer')
            ->setEntityLabelInPlural('Customer')
            ->setSearchFields(['firstName', 'lastName', 'email'])
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable('new');
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel('Main info')->setIcon('fa fa-info')->setCssClass('col-sm-6');

        yield IntegerField::new('id')->setFormTypeOption('disabled', 'disabled')->hideWhenCreating();
        $roles = [ 'ROLE_CUSTOMER' ];
        yield ChoiceField::new('roles')
            ->setChoices(array_combine($roles, $roles))
            ->allowMultipleChoices()
            ->renderAsBadges()
            ->hideOnIndex()
            ->setColumns('col-md-8');
        //yield ArrayField::new('roles')->hideOnIndex()->setFormTypeOption('disabled', 'disabled');
        yield BooleanField::new('hidden');
        yield BooleanField::new('isVerified')->setLabel('Is verified');
        yield BooleanField::new('phoneVerified')->setLabel('SMS verified')->hideOnIndex();
        yield BooleanField::new('isActive')->setLabel('Active user');
        yield TextField::new('firstName')->setColumns('col-md-10');
        yield TextField::new('lastName')->setColumns('col-md-10');
        //yield TextField::new('email')->setColumns('col-md-10');
        yield TextField::new('username')->hideOnIndex()->setColumns('col-md-10');
        //yield TextField::new('password')->setFormType(PasswordType::class)->hideOnIndex();
        yield EmailField::new('email')->setColumns('col-md-10');
        yield ImageField::new('avatar')
            ->setBasePath('uploads/files/')
            ->setUploadDir('public_html/uploads/files')
            ->setFormType(FileUploadType::class)
            ->setRequired(false);

        yield FormField::addPanel('General information')->setIcon('fa fa-info-circle')->setCssClass('col-sm-6');
        yield TextareaField::new('about')->hideOnIndex()->setColumns('col-md-10');
        yield AssociationField::new('city')->hideOnIndex()->setColumns('col-md-10');
        yield TextareaField::new('address')->hideOnIndex()->setColumns('col-md-10');
        yield TelephoneField::new('phone')->hideOnIndex()->setColumns('col-md-10');
        yield IntegerField::new('age')->hideOnIndex()->setColumns('col-md-10');

        /*yield FormField::addPanel('Change password')->setIcon('fa fa-key')->setCssClass('col-sm-12');
        yield FormField::addRow();
        yield Field::new('password', 'New password')->onlyWhenCreating()->setRequired(true)
            ->setFormType(RepeatedType::class)
            ->setRequired(false)
            ->setFormTypeOptions([
                'type'            => PasswordType::class,
                'first_options'   => [ 'label' => 'New password' ],
                'second_options'  => [ 'label' => 'Repeat password' ],
                'error_bubbling'  => true,
                'invalid_message' => 'The password fields do not match.',
            ]);
        yield Field::new('password', 'New password')->onlyWhenUpdating()->setRequired(false)
            ->setFormType(RepeatedType::class)
            ->setRequired(false)
            ->setFormTypeOptions([
                'type'            => PasswordType::class,
                'first_options'   => [ 'label' => 'New password' ],
                'second_options'  => [ 'label' => 'Repeat password' ],
                'error_bubbling'  => true,
                'invalid_message' => 'The password fields do not match.',
            ]);*/

        yield FormField::addPanel('Relations')->setIcon('fa fa-chain')->setCssClass('col-sm-12');
        yield FormField::addRow();
        yield ChoiceField::new('experience')->setChoices(
            [
                'Нет' => null,
                'Менее 6 месяцев' => '1',
                '6-12 месяцев' => '2',
                '2-5 лет' => '3',
                'более 5 лет' => '4',
            ]
        )->hideOnIndex()
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
        )->hideOnIndex()
            ->setColumns('col-md-4');
        /*yield ChoiceField::new('citizen')->setChoices(
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
        )->hideOnIndex()
            ->setColumns('col-md-4');*/

        yield AssociationField::new('citizen')->hideOnIndex()->setColumns('col-md-4');

        yield FormField::addRow();
        yield AssociationField::new('category')->hideOnIndex()->setColumns('col-md-4');

        yield AssociationField::new('tariff')->hideOnIndex()->setColumns('col-md-4')
            ->setFormTypeOption('disabled', 'disabled');
        yield AssociationField::new('orders')->hideOnIndex()->setColumns('col-md-4')
            ->setFormTypeOption('disabled', 'disabled');

        yield FormField::addRow();
        yield AssociationField::new('jobs')->hideOnIndex()->setColumns('col-md-4');
        yield AssociationField::new('worksheets')->hideOnIndex()->setColumns('col-md-4');
        yield AssociationField::new('featuredProfiles')
            ->setFormTypeOptions([
                'by_reference' => false,
            ])->hideOnIndex()
            ->setColumns('col-md-4');
        yield AssociationField::new('featuredJobs')
            ->setFormTypeOptions([
                'by_reference' => false,
            ])->hideOnIndex()
            ->setColumns('col-md-12');
    }
}
