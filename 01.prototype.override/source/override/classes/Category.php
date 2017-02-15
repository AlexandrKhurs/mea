<?php
/**
 * ...., the part or Mass Edit Anything (MEA) project
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
class Category extends CategoryCore {
    
    /** returns category path from HOME category
     * 
     * @param int $cat_id
     * @param string $separator (means nothing if $as_array==true)
     * @param string $include_home
     * @param int $limit
     * @param bool $as_array - return string or array
     * @param bool $return_ids - return ids instead of names
     * @param int $loop_limit - protection against forever loop if data integrity broken
     * @return string
     */
    public static function getPath($cat_id, $separator = '/', $include_home = true, $limit = 0, 
                                   $as_array = false, $return_ids = false, $loop_limit = 20) {
        $context = Context::getContext();
        $catname = array();
        do {
            if($cat_id == (int)Configuration::get('PS_HOME_CATEGORY') && !$include_home)
                break;
            $cat = new Category($cat_id);
            array_push($catname, $return_ids ? $cat->id : $cat->name[$context->language->id]);
            if($cat_id == (int)Configuration::get('PS_HOME_CATEGORY') && $include_home)
                break;
            $cat_id = $cat->id_parent;
            $loop_limit--;
        } while ($cat_id && $loop_limit);
        $catname = array_reverse($catname);
        if($limit)
            array_splice($catname, $limit);
        return $as_array ? $catname : implode($separator, $catname);
    }
}
