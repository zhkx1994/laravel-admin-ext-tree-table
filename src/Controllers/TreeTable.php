<?php

namespace Zhkx1994\TreeTable\Controllers;

class TreeTable
{
    protected $data;

    protected $columns;

    protected $operates = [];

    protected $createBtn = true;

    protected $editBtn = true;

    protected $showBtn = true;

    protected $deleteBtn = true;

    protected $tools = [];

    public function __construct($data)
    {
        $this->data = $data;

        $this->setActions();

        // 初始化行
//        $this->columns[] = ['field' => 'check', 'checkbox' => true, 'formatter' => 'checkFormatter'];
    }

    public function addToolBtn($text, $url, $class = '')
    {
        $this->tools[] = [
            'text' => $text,
            'class' => $class,
            'url' => $url,
        ];
    }

    public function display($fun)
    {
        $index = count($this->columns) - 1;
        $field = $this->columns[$index]['field'];
//        $this->columns[$index]['formatter'] = 'imageFormatter';

        foreach ($this->data as & $datum) {
            $content = $fun($datum[$field]);
            $datum[$field] = str_replace('"', '\'', $content);
        }

        return $this;
    }

    /**
     * 添加行
     */
    public function column($field, $title)
    {
        $this->columns[] = ['field' => $field, 'title' => $title];
        return $this;
    }


    public function image($width = '', $height = '')
    {
        $index = count($this->columns) - 1;
        $field = $this->columns[$index]['field'];
        $this->columns[$index]['formatter'] = 'imageFormatter';

        foreach ($this->data as & $datum) {
            $datum[$field] = ['val' => $datum[$field]];
            $datum[$field]['width'] = $width;
            $datum[$field]['height'] = $height;
        }

        return $this;
    }

    public function label($data)
    {
        $index = count($this->columns) - 1;
        $field = $this->columns[$index]['field'];
        $this->columns[$index]['formatter'] = 'labelFormatter';

        foreach ($this->data as & $datum) {
            $datum[$field] = [
                'val' => $data[$datum[$field]]['text'],
                'class' => 'label label-' . ($data[$datum[$field]]['color'] ?? 'primary')
            ];
        }

        return $this;
    }

    public function using($data)
    {
        $index = count($this->columns) - 1;
        $field = $this->columns[$index]['field'];
        foreach ($this->data as & $datum) {
            $datum[$field] = $data[$datum[$field]] ?? '无';
        }
        return $this;
    }

    /**
     * 禁用新增操作
     */
    public function disableCreateBtn()
    {
        $this->createBtn = false;
        return $this;
    }

    /**
     * 禁用查看操作
     */
    public function disableShowBtn()
    {
        $this->showBtn = false;
        return $this;
    }

    /**
     * 禁用删除操作
     */
    public function disableDeleteBtn()
    {
        $this->deleteBtn = false;
        return $this;
    }

    /**
     * 禁用编辑操作
     */
    public function disableEditBtn()
    {
        $this->editBtn = false;
        return $this;
    }

    public function addActionBtn($text, $url, $class = '')
    {
        $this->operates[] = [
            'text' => $text,
            'class' => $class,
            'url' => $url,
            'action' => '',
            'style' => 'margin-right: 1rem;',
        ];
    }

    /**
     * 操作栏
     */
    private function setActions()
    {
        // 查看按钮
        if ($this->showBtn) {
            $this->operates[] = [
                'text' => "",
                'style' => 'margin-right: 1rem;',
                'class' => 'grid-row-view btn btn-xs btn-info',
                'action' => 'show',
            ];
        }

        // 编辑按钮
        if ($this->editBtn) {
            $this->operates[] = [
                'text' => "",
                'style' => 'margin-right: 1rem;',
                'class' => 'grid-row-edit btn btn-xs btn-primary',
                'action' => 'edit',
            ];
        }

        // 删除按钮
        if ($this->deleteBtn) {
        }

        if (!empty($this->operates)) {
            $this->columns[] = [
                'field' => 'operate',
                'title' => '操作',
                'align' => 'left',
                'formatter' => 'operateFormatter',
            ];
        }
    }

    public function render()
    {
        return view('treetable::index', [
            'data' => $this->data,
            'columns' => $this->columns,
            'operates' => $this->operates,
            'tools' => $this->tools,
        ]);
    }
}
