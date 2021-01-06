<!DOCTYPE HTML>
<html lang="zh-cn">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width,initial-scale=1.0" name="viewport">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <title>树形表格</title>

    <link rel="stylesheet" type="text/css" href="{{ asset('/vendor/zhkx1994/laravel-admin-ext-tree-table/treetable/css/bootstrap-table.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/vendor/zhkx1994/laravel-admin-ext-tree-table/treetable/css/jquery.treegrid.css') }}">
</head>

<body>
<section class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="pull-right">
                    <div class="btn-group pull-right grid-create-btn" style="margin-right: 10px">
                        @foreach($tools as $tool)
                            <a href="{{ url($tool['url']) }}" class="btn btn-sm {{ $tool['class'] }}" title="{{ $tool['text'] }}">
                                <span class="hidden-xs">&nbsp;&nbsp;{{ $tool['text'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="box-body ">
                <table class="table table-striped" id="grid-table"></table>
            </div>
            <div class="box-footer clearfix">

            </div>
        </div>
    </div>
</section>
</body>
<script src="{{ asset('/vendor/zhkx1994/laravel-admin-ext-tree-table/treetable/js/bootstrap-table.js') }}"></script>
<script src="{{ asset('/vendor/zhkx1994/laravel-admin-ext-tree-table/treetable/js/bootstrap-table-treegrid.js') }}"></script>
<script src="{{ asset('/vendor/zhkx1994/laravel-admin-ext-tree-table/treetable/js/jquery.treegrid.js') }}"></script>
<script type="text/javascript">

    var $table = $('#grid-table');

    var dataJsonStr = `{{ json_encode($data) }}`.replace(/[\r\n]/g,"").replace(/&#039;/g,"\'");;
    dataJsonStr = escape2Html(dataJsonStr);
    var data = JSON.parse(dataJsonStr);

    var columnsJsonStr = '{{ json_encode($columns) }}'.replace(/[\r\n]/g,"");
    columnsJsonStr = escape2Html(columnsJsonStr);
    var columns = JSON.parse(columnsJsonStr);

    var operatesJsonStr = '{{ json_encode($operates) }}'.replace(/&#039;/g,"\'");
    operatesJsonStr = escape2Html(operatesJsonStr);
    var operates = JSON.parse(operatesJsonStr);

    var initialState = '{{ $initialState }}';

    //转意符换成普通字符
    function escape2Html(str) {
        var arrEntities={'lt':'<','gt':'>','nbsp':' ','amp':'&','quot':'"'};
        return str.replace(/&(lt|gt|nbsp|amp|quot);/ig,function(all,t){return arrEntities[t];});
    }

    $(function() {
        $table.bootstrapTable({
            data:data,
            idField: 'id',
            dataType:'jsonp',
            columns: columns,

            // bootstrap-table-treegrid.js 插件配置 -- start
            //在哪一列展开树形
            treeShowField: 'id',
            //指定父id列
            parentIdField: 'pid',
            // search: true,
            onResetView: function(data) {
                $table.treegrid({
                    initialState: initialState,// 所有节点都折叠
                    // initialState: 'expanded',// 所有节点都展开，默认展开
                    treeColumn: 1,
                    expanderExpandedClass: 'glyphicon glyphicon-menu-right',  //图标样式
                    expanderCollapsedClass: 'glyphicon glyphicon-menu-down',
                    cardView: false,                    //是否显示详细视图
                    detailView: false,                   //是否显示父子表
                    onChange: function() {
                        $table.bootstrapTable('resetWidth');
                    }
                });

                //只展开树形的第一级节点
                // $table.treegrid('getRootNodes').treegrid('expand');
            },
            onCheck:function(row){
                var datas = $table.bootstrapTable('getData');
                // 勾选子类
                selectChilds(datas,row,"id","pid",true);
                // 勾选父类
                selectParentChecked(datas,row,"id","pid")
                // 刷新数据
                $table.bootstrapTable('load', datas);
            },
            onUncheck:function(row){
                var datas = $table.bootstrapTable('getData');
                selectChilds(datas,row,"id","pid",false);
                $table.bootstrapTable('load', datas);
            },
            // bootstrap-table-treetreegrid.js 插件配置 -- end
        });
    });

    /**
     * 选择内容
     */
    function checkFormatter(value, row, index) {
        if (row.check == true) {
            // console.log(row.serverName);
            //设置选中
            return {  checked: true };
        }
    }

    /**
     * 显示图片
     */
    function imageFormatter(value, row, index) {
        if (value.val !== '') {
            return '<img class="img img-thumbnail" src="' + value.val + '" height="' + value.height + '" width="' + value.width + '"/>';
        }
        return '';
    }

    function labelFormatter(value, row, index) {
        return '<span class="' + value.class + '" >' + value.val + '</span>';
    }

    // 格式化按钮
    function operateFormatter(value, row, index) {
        var html = '';
        var url = window.location.href;

        for (var i=0; i < operates.length; i++) {
            var btn = operates[i];
            switch(btn.action) {
                case 'show':
                    html += '<a class="' + btn.class + '" href="' + url + '/' + row.id + '" style="' + btn.style + '" >' + '查看' + '</a>';
                    break;
                case 'edit':
                    html += '<a class="' + btn.class + '" href="' + url + '/' + row.id + '/edit" style="' + btn.style + '" >' + '编辑' + '</a>';
                    break;
                default:
                    html += '<a class="' + btn.class + '" href="' + btn.url + '?id=' + row.id +'" style="' + btn.style + '" >' + btn.text + '</a>';
            }
        }

        return html;
    }

    // 格式化类型
    function typeFormatter(value, row, index) {
        if (value === 'menu') {  return '菜单';  }
        if (value === 'button') {  return '按钮'; }
        if (value === 'api') {  return '接口'; }
        return '-';
    }
    // 格式化状态
    function statusFormatter(value, row, index) {
        if (value === 1) {
            return '<span class="label label-success">正常</span>';
        } else {
            return '<span class="label label-default">锁定</span>';
        }
    }

    //初始化操作按钮的方法
    window.operateEvents = {
        'click .RoleOfshow': function (e, value, row, index) {
            var url = window.location.href;
            window.location.href = url + '/' + row.id;
        },
        'click .RoleOfdelete': function (e, value, row, index) {
            del(row.id);
        },
        'click .RoleOfedit': function (e, value, row, index) {
            var url = window.location.href;
            window.location.href = url + '/' + row.id + '/edit';
        }
    };


    /**
     * 选中父项时，同时选中子项
     * @param datas 所有的数据
     * @param row 当前数据
     * @param id id 字段名
     * @param pid 父id字段名
     */
    function selectChilds(datas,row,id,pid,checked) {
        for(var i in datas){
            if(datas[i][pid] == row[id]){
                datas[i].check=checked;
                selectChilds(datas,datas[i],id,pid,checked);
            };
        }
    }

    function selectParentChecked(datas,row,id,pid){
        for(var i in datas){
            if(datas[i][id] == row[pid]){
                datas[i].check=true;
                selectParentChecked(datas,datas[i],id,pid);
            };
        }
    }

    function show(id) {
        alert('show方法, id = ' + id);
        {{--window.location.href = '{{$showUrl}}';--}}
    }
    function del(id) {
        alert("del 方法 , id = " + id);
        {{--window.location.href = '{{$deleteUrl}}';--}}
    }
    function update(id) {
        alert("update 方法 , id = " + id);
        {{--window.location.href = '{{$createUrl}}';--}}
    }
</script>
</html>
