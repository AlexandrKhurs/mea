<?php 
/**
 * Mass-edit Categories, the part or Mass Edit Anything (MEA) project
 * https://github.com/AlexandrKhurs/mea
 *
 * NOTICE OF LICENSE
 * This source file is distributed under GNU General Public License (GPL 3.0).
 * Please refer to https://opensource.org/licenses/GPL-3.0 for more information.
 *
 * DISCLAIMER
 * This source code is distributed "as is", with no warranty expressed or implied, 
 * and no guarantee for accuracy or applicability to any purpose. 
 * 
 *  @author Alexander Khurs <alexandr[dot]khurs[at]gmail[dot]com>
 *  @license https://opensource.org/licenses/GPL-3.0  GNU General Public License (GPL 3.0)
 */
class AdminCategoriesController extends AdminCategoriesControllerCore {
    public function __construct() {
        parent::__construct();
        $this->bulk_actions['movetocat']  = 
            [
                'text' => $this->l('Move to category'),
                'icon' => 'icon-arrow-right',
            ];
    }
    
    public function initProcess() {
        parent::initProcess();
        if(in_array($this->action, ['movetocat', 'bulkmovetocat']) && !Tools::getValue('categoryTo', 0))
            $this->action .= '_select';
    }
    
    public function renderList() {
        $output = '';//.$this->action;
        if(in_array($this->action, ['movetocat_select', 'bulkmovetocat_select']))
            $output .= $this->renderCategoryChooseForm(
                    $this->l('Move to category'), 
                    $this->l('Remove categories under this category'), 
                    'categoryTo',
                    str_replace('_select', '', $this->action));
        
        $output .= parent::renderList();
        return $output;
    }
    
    protected function renderCategoryChooseForm($title, $desc, $paramName, $action) {
        $categories = array_map(
            function($c) {
                $c['path'] = Category::getPath($c['id_category'], ' | ', false);
                return $c;
            },
            Category::getAllCategoriesName(null, false, false)
        );
        foreach($categories as $ckey => $cat)
            if($cat['id_category'] == (int)Configuration::get('PS_HOME_CATEGORY') || $cat['id_category'] == (int)Configuration::get('PS_ROOT_CATEGORY'))
                unset($categories[$ckey]);
        usort($categories, function($a, $b) {
            if($a['path'][0] !== '@' && $b['path'][0] === '@')
                return -1;
            if($a['path'][0] === '@' && $b['path'][0] !== '@')
                return 1;
            if($a['path'] == $b['path'])
                return 0;
            return strcmp($a['path'], $b['path']);
        });
        array_unshift($categories, ['path'=> $this->l('Home'), 'id_category'=>  Configuration::get('PS_HOME_CATEGORY')]);
        array_unshift($categories, ['path'=> ' - choose category - ', 'id_category'=>0]);
        
        
        $fields_form = [];
        $fields_form[0]['form'] = array(
            'legend' => array(       
              'title' => $title,
              //'image' => '../img/admin/icon_to_display.gif'   
            ),   
            'input' => array(       
                array(           
                    'name' => $paramName,
                    'desc' => $desc,
                    'type' => 'select',
                    'options' => array(
                        'query' => $categories,
                        'id' => 'id_category',
                        'name' => 'path',   
                    ),
                 ),
            ),
            'submit' => array(
                'title' => $this->l('Proceed'),       
                'class' => 'btn btn-default'  ,
                'icon' => 'icon-chevron-right pull-right'
            ),
            'buttons' => array(
                array(
                    'href' => AdminController::$currentIndex.'&cancel=1&token='.Tools::getAdminTokenLite('AdminCategories'),
                    'title' => $this->l('Cancel'),
                    'class' => 'btn btn-default pull-left',
                    'icon' => 'icon-remove pull-right'
                )
            ),
        );
        
        $helper = new HelperForm();
        // Module, token and currentIndex
        $helper->name_controller = 'AdminCategories';
        $helper->token = Tools::getAdminTokenLite('AdminCategories');
        $helper->currentIndex = AdminController::$currentIndex.(isset($_GET['id_category'])?'&id_category='.(int)$_GET['id_category'] : '');

        // Title and toolbar
        $helper->title = $title;
        $helper->show_toolbar = false;        // false -> remove toolbar
        $helper->toolbar_scroll = false;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit'.ucfirst($action).'category';
        $helper->fields_value[$paramName] = isset($_POST[$paramName]) ? (int)$_POST[$paramName] : (int)Configuration::get('PS_HOME_CATEGORY');
        
        foreach($_POST as $pk => $pv) {
            if(in_array($pk, [$paramName]))
                continue;
            
            if(is_array($pv)) {
                foreach($pv as $pvk => $pvv) {
                    $fields_form[0]['form']['input'][] = ['name'=>$pk.'['.$pvk.']', 'type'=>'hidden'];
                    $helper->fields_value[$pk.'['.$pvk.']'] = (string)$pvv;
                }
            } else {
                $fields_form[0]['form']['input'][] = ['name'=>$pk, 'type'=>'hidden'];
                $helper->fields_value[$pk] = (string)$pv;
            }
        }
        
        return  $helper->generateForm($fields_form);

    }
    
    
    protected function processBulkMovetocat() {
        if ($this->tabAccess['edit'] !== '1') 
            $this->errors[] = 'You do not have permission to edit this.';
            
        if(!count($this->errors)) {
            $category_to = (int)Tools::getValue('categoryTo', 0);
            if (!is_array($this->boxes) || empty($this->boxes)) 
                $this->errors[] = 'Select at least one category to move';
            if (!$category_to) 
                $this->errors[] = 'Select category to move to';
            
            if(!count($this->errors)) {
                try {
                    Db::getInstance()->query('START TRANSACTION');
                    $counter = 0;
                    foreach($this->boxes as $id_category) {
                        $category = new Category($id_category);
                        if(!Validate::isLoadedObject($category))
                            throw new Exception('Cant load category (id='.(int)$category.')');
                        
                        $category->id_parent = (int)$category_to;
                        if(!$category->update())
                            throw new Exception('cant update category (id='.$id_category.')');
                        
                        $counter++;
                    }
                    Db::getInstance()->query('COMMIT');
                    $this->confirmations[] = 'Updated '.$counter.' categories';
                } catch(Exception $e) {
                    Db::getInstance()->query('ROLLBACK');
                    $this->errors[] = $e->getMessage();
                }
            }
        }
        
        // set action back to select-mode
        if(count($this->errors))
            $this->action .= '_select';
    }
}
