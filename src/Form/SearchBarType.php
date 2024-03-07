<?php

namespace App\Form;

use App\Data\SearchBarData;
use App\Entity\Widget;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchBarType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('type', HiddenType::class, ['data' => 'searchBar'])

      ->add('from', DateType::class, ['required' => false])
      ->add('to', DateType::class, ['required' => false])

      ->add('track', TextType::class, ['required' => false])
      ->add('artist', TextType::class, ['required' => false])
      ->add('album', TextType::class, ['required' => false])
      ->setAttribute('onclick', 'return false;')
      ->setAttribute('data-stripe', 'name')

      /** Set the dateFrom and dateTo if only one date is set */
      ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event): void {

        /** @var Widget $widget */
        $dataSearchBar = $event->getData();

        $from = $dataSearchBar->from;
        $to = $dataSearchBar->to;

        if ($from != null && $to == null) {
          //set to current date + 1 day in DateTime format
          $dataSearchBar->to = new \DateTime('now +1 day');
        }
        if ($from == null && $to != null) {
          //set from to 1970-01-01 in DateTime format
          $dataSearchBar->from = new \DateTime('1970-01-01');
        }
      })
    ;
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => SearchBarData::class,
    ]);
  }
}
