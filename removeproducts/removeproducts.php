<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class removeproducts extends  Module{

    private $templateFile;

    public function __construct()
    {

        $this->name = 'removeproducts';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Daniel Rodríguez';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->trans('Eliminar todos los productos', array(), 'Modules.removeproducts.Admin');
        $this->description = $this->trans('Elimina todos los productos de la web.', array(), 'Modules.removeproducts.Admin');

        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_);

        $this->templateFile = 'module:removeproducts/removeproducts.tpl';
    }
    public function install()
    {
        return parent::install();
    }



    public function uninstall()
    {
        return parent::uninstall();
    }


    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submit'.$this->name)) {
            $removeProduct = strval(Tools::getValue('MYMODULE_NAME'));

            $res = Db::getInstance()->executeS('SELECT `id_product` FROM `'._DB_PREFIX_.'product` ORDER BY `id_product` DESC LIMIT 100 ');
            $output .= "<p>(".date('Y/m/d H:i:s').") Comenzando a borrar productos...</p>";
            if ($res) {
                foreach ($res as $row) {
                    $output .= "<p>(".date('Y/m/d H:i:s').") Borrando Producto con ID <b>".$row['id_product']."</b>...";
                    $p = new Product($row['id_product']);
                    if(!$p->delete()) {
                        $output .= " <span style='color: red'>Error borrando este producto!</span></p>";
//                        echo " <span style='color: red'>Error deleting this product!</span></p>";
                    } else {

                        $output .= "<span style='color: green'>Borrado</span></p>";
//                        echo " <span style='color: green'>DELETED</span></p>";
                    }
                }

            $output .= $this->displayConfirmation($this->l('Productos borrados con exito'));
            }
//            $output .= "<pre>".var_dump($res)."</pre>";
//            $output .= $this->displayConfirmation($this->l('Settings updated'));
        }

        return $output.$this->displayForm();
    }

    public function displayForm()
    {
        // Get default language
        $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');

        // Init Fields form array
        $fieldsForm[0]['form'] = [
            'legend' => [
                'title' => $this->l('¿Quiere eliminar todos los productos?'),
            ],
            'submit' => [
                'title' => $this->l('Borrar'),
                'class' => 'btn btn-default pull-right'
            ]
        ];

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        // Language
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = [
            'save' => [
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                    '&token='.Tools::getAdminTokenLite('AdminModules'),
            ],
            'back' => [
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            ]
        ];

        // Load current value
        $helper->fields_value['MYMODULE_NAME'] = Configuration::get('MYMODULE_NAME');

        return $helper->generateForm($fieldsForm);
    }

}