<?php

namespace App\Controller\Admin;

use App\Entity\Notification;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class NotificationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Notification::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Notification')
            ->setEntityLabelInPlural('Notification')
            ->setSearchFields(['id'])
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('user'))
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id')->setFormTypeOption('disabled', 'disabled');
        yield DateTimeField::new('created')->setFormTypeOption('disabled', 'disabled');
        yield BooleanField::new('isRead');
        yield TextField::new('message');
        yield TextField::new('type')->hideOnDetail();
        yield ChoiceField::new('type')->setChoices(
            [
                'Тип уведомления' => null,
                //'Изменения статуса заявки (в работе, завершена)' => '1',
                //'Баланс пополнен на сумму' => '2',
                //'С баланса списание на сумму' => '3',
                //'Новая заявка в системе по вашему городу и профессии' => '4',
                //'Массовая рассылка' => '10'
            ]
        )->hideOnIndex();
        yield AssociationField::new('user')->hideOnIndex();
        //yield AssociationField::new('application')->hideOnIndex();
    }
}
