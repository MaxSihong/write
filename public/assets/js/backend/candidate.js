define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'candidate/index' + location.search,
                    add_url: 'candidate/add',
                    edit_url: 'candidate/edit',
                    del_url: 'candidate/del',
                    import_url: 'candidate/import',
                    multi_url: 'candidate/multi',
                    table: 'candidate',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'name', title: __('Name')},
                        // {field: 'phone', title: __('Phone')},
                        {field: 'candidate_number', title: __('Candidate_number')},
                        {field: 'seat_number', title: __('Seat_number')},
                        {field: 'candidate_region', title: __('Candidate_region')},
                        {field: 'number', title: __('Number')},
                        {field: 'user_id', title: __('是否认证')},
                        {field: 'is_check', title: __('Is_check')},
                        {field: 'create_at', title: __('Create_at')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});