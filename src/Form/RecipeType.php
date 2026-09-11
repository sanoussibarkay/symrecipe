<?php

namespace App\Form;

use \Symfony\Component\Form\Extension\Core\Type\RangeType;
use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Repository\IngredientRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;    
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Form\Type\VichImageType;


class RecipeType extends AbstractType
{

private $security;

    public function __construct(Security $security,)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
             ->add('name', TextType::class,
                [
                    'attr' => [
                        'class' => 'form-control',
                        'minlength' => 2,
                        'maxlength' => 50
                    ],
                    'label' => 'Nom de l\'ingrédient',
                    'label_attr' => [
                        'class' => 'form-label mt-4',
                    ],
                    'constraints' => [
                       new Assert\Length(['min' => 2, 'max' => 50]),
                       new Assert\NotBlank(),
                       ],
                ])
            ->add('time', IntegerType::class,
                [
                    'attr' => [
                        'class' => 'form-control',
                        'min' => 1,
                        'max' => 1440
                    ],
                    'label' => 'Temps de préparation (en minutes)',
                    'label_attr' => [
                        'class' => 'form-label mt-4',
                    ],
                    'constraints' => [
                        new Assert\Positive(),
                        new Assert\LessThan(1441)
                    ],
                ])
            ->add('nbPeople', IntegerType::class,
                [
                    'attr' => [
                        'class' => 'form-control',
                        'min' => 1,
                        'max' => 50
                    ],
                    'label' => 'Nombre de personnes',
                    'label_attr' => [
                        'class' => 'form-label mt-4',
                    ],
                    'constraints' => [
                        new Assert\Positive(),
                        new Assert\LessThan(51)
                    ],
                ])
            ->add('difficulty', RangeType::class,
                [
                    'attr' => [
                        'class' => 'form-range',
                        'min' => 1,
                        'max' => 5
                    ],
                    'label' => 'Difficulté',
                    'label_attr' => [
                        'class' => 'form-label mt-4',
                    ],
                    'constraints' => [
                        new Assert\Positive(),
                        new Assert\LessThan(51)
                    ],
                ])
            
            ->add('description', TextareaType::class,
                [
                    'attr' => [
                        'class' => 'form-control',
                    ],
                    'label' => 'Description de la recette',
                    'label_attr' => [
                        'class' => 'form-label mt-4',
                    ],
                    'constraints' => [
                        new Assert\NotBlank(),
                    ],
                ])
             ->add('price', MoneyType::class,
                [
                    'attr' => [
                        'class' => 'form-control',
                    ],
                    'label' => 'Prix de l\'ingrédient',
                    'label_attr' => [
                        'class' => 'form-label mt-4',
                    ],
                    'constraints' => [
                        new Assert\Positive(),
                        new Assert\LessThan(1001)
                    ],
                ])
            
            ->add('isFavorite', CheckboxType::class, [
                'label' => 'Favori',
                'label_attr' => [
                    'class' => 'form-label mt-4',
                ],
                'required' => false,
            ])
            ->add('ingredients', EntityType::class, [
                'class' => Ingredient::class,
                'query_builder' => function (IngredientRepository $er) {
                    return $er->createQueryBuilder('i')
                            ->where('i.user = :user')
                            ->orderBy('i.name', 'ASC')
                            ->setParameter('user', $this->security->getUser());
                },
                'label' => 'Ingrédients',
                'label_attr' => [
                    'class' => 'form-label mt-4',
                ],
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
               
                
            ])
            ->add('imageFile', VichImageType::class , [
                'label' => 'Image de la recette',
                'label_attr' => [
                    'class' => 'form-label mt-4',
                ],
                'required' => false,
                'allow_delete' => true,
                'download_uri' => false,
                'image_uri' => true,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('submit',SubmitType::class, [
                'label' => $builder->getData()->getId() ? 'Modifier la recette' : 'Créer une recette',
                'attr' => [
                    'class' => 'btn btn-primary mt-3',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
