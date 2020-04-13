<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Colory;
use App\Entity\Size;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder            
            ->add('title', TextType::class, ['required' => false])
            ->add('picture', FileType::class, [
                'required' => false,
                'data_class' => null
            ])
            ->add('colory', EntityType::class, [
                'class' => Colory::class,
                'choice_label' => 'label',
                'multiple' => true,
                'expanded' => false,
                'required' => false
            ])            
            ->add('tarif', TextType::class, ['required' => false])
            ->add('isPublished', CheckboxType::class, ['label'    => 'publié','required' => false,])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'label',
                'multiple' => true,
                'expanded' => false,
                'required' => false
            ]) 
            ->add('size', EntityType::class, [
                'class' => Size::class,
                'choice_label' => 'label',
                'multiple' => true,
                'expanded' => false,
                'required' => false
            ])               
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
