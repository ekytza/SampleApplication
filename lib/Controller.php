<?php

namespace SampleApplication;


class Controller
{
    use Validator;

    public $view;
    protected $data = array();

    public function __construct()
    {
        $this->view = new View();
    }

    /**
         * Application start method
    */
    public Function start()
    {
        try  {
            list ($route, $var) = $this->Validate();
            if (!$route)    throw new \Exception("Invalid query param: ".$var, 1);

            if (method_exists($this, $route))    $this->$route();
            else throw new \Exception("Unknown method: ".$route, 1);

        }  catch (\Exception $e)  {
            echo json_encode(array('error' => $e->getMessage()));
        }

    }

    /**
         * Default control
    */
    protected function index()
    {
        $p = new Product();
        $c = new Category();
        $this->data['tree'] = $c->getTree($c->getRoot());
        $this->data['brandList'] = $p->getBrandList();
        $this->view->show($this->data);
    }

    /**
         * Category expaner method
    */
    protected function expand()
    {
        $c = new Category();
        $this->data = $c->getTree($this->validid);
        $this->view->showJSON($this->data);
    }

    /**
         * View product by brand method
    */
    protected function viewbrand()
    {
        $p = new Product();
        $this->data = $p->get('brand', $this->validbrand);
        $this->view->showJSON($this->data);
    }

    /**
         * View product by category method
    */
    protected function viewcat()
    {
        $cpr = new CategoryProductRel();
        $p = new Product();
        $list = $cpr->getRel('cat', $this->validid);
        $this->data = $p->get('cat', $list);
        $this->view->showJSON($this->data);
    }

    /**
         * View product by category and subcategories method
    */
    protected function viewall()
    {
        $c = new Category();
        $cpr = new CategoryProductRel();
        $p = new Product();
        $tree = $c->getTree($this->validid);
        $list = $cpr->getRelAll($tree);
        $this->data = $p->get('cat', $list);
        $this->view->showJSON($this->data);
    }

    /**
         * View one product method
         *
         * @param строка $message
         * @param массив $context
         * @return null
    */
    function getitem()
    {
        $p = new Product();
        $this->data = $p->get('id', $this->validid);
        $this->view->showJSON($this->data);
    }

    /**
         * Context search method
    */
    function match()
    {
        $p = new Product();
        $this->data = $p->get('match', $this->validq);
        $this->view->showJSON($this->data);
    }
}
