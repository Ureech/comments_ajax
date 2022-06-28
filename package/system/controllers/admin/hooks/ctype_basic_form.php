<?php

class onAdminCtypeBasicForm extends cmsAction
{

    public function run($form)
    {

        $structure = $form->getStructure();
        $fieldset_id ='comments';
        if ($structure['comments']) {
    
            $form->addField($fieldset_id, new fieldString('count_comm', array(
                'title' => 'Количество комментариев для показа',
                'default' => 10,
                'hint' => 'Количество комментариев до показа ссылки на следующий пакет комментариев',
                'visible_depend' => array('is_comments' => array('show' => array('1')))
            )));

            $form->addField($fieldset_id, new fieldCheckbox('is_rating_newcomm', array(
                'title' => 'Рейтинг',
                'default' => 0,
                'hint' => 'Выводить рейтинг в комментариях',
                'visible_depend' => array('is_comments' => array('show' => array('1')))
            )));

            $form->addField($fieldset_id, new fieldCheckbox('is_score_newcomm', array(
                'title' => 'Оценка',
                'default' => 0,
                'hint' => 'Выводить оценку',
                'visible_depend' => array('is_comments' => array('show' => array('1')))
            )));

            $form->addField($fieldset_id, new fieldCheckbox('is_likes_newcomm', array(
                'title' => 'Лайки',
                'default' => 0,
                'visible_depend' => array('is_comments' => array('show' => array('1')))
            )));
        }

        return $form;
    }
}
