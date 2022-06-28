<?php

class onCommentsContentNewsBeforeItem extends cmsAction {

    public function run($data){

        list($ctype, $item, $fields) = $data;

        if ($ctype['is_comments'] && $item['is_approved'] && !empty($item['is_comments_on'])){

            $tool_buttons['edit'] = [
                'title'   => 'Удалить дополнение',
                'options' => ['class' => 'вудуеу', 'icon' => 'minus-circle'],
                'url'     => href_to($this->name, 'delete_newcomm')
            ];
            $this->cms_template->addMenuItems('toolbar', $tool_buttons);

        }

        return array($ctype, $item, $fields);

    }


}
