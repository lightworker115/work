<?php
/**
 * file: admin.php
 * desc: 语言配置文件
 * user: liujx
 * date: 2016-07-19
 */
return [
    'STATUS_ACTIVE'                                          => '启用',
    'STATUS_INACTIVE'                                        => '禁用',
    'STATUS_DELETED'                                         => '删除',
    'Are you sure you want to delete this item?'             => '您确定要删除此项吗？',
    'Incorrect username or password.'                        => '帐号或密码不正确',
    'The file "{file}" is not an image.'                     => '文件 "{file}" 不是一个图像文件。',
    'Hello, {name}'                                          => '你好,{name}',
    'The Administrator has all permissions'                  => '该管理员拥有所有权限',
    'You can not modify the super administrator privileges'  => '不能修改超级管理员权限',
    'Sorry, you do not have permission to modify this role!' => '对不起，您没有修改该角色的权限!',
    'successfully updated'                                   => '更新成功',
    'successfully removed'                                   => '删除成功',
    'still used'                                             => '还在使用',
    'Directly Input Time'                                    => '可直接输入日期，格式：2015-01-01',
    'No file upload'                                         => '没有文件上传',
    'Directory creation failed'                              => '目录创建失败',
    'Select county'                                          => '选择县',
    'Successfully processed'                                 => '处理成功',
    'Processing failed'                                      => '处理失败',
    'successfully deleted'                                   => '删除成功',
    'failed to delete'                                       => '删除失败',
    'please choose'                                          => '请选择',
    'Create'                                                 => '创建',
    'Save'                                                   => '保存',
    'Update'                                                 => '更新',
    'error'                                                  => '错误',
    'close all'                                              => '关闭全部',
    'error_code'                                             => [
        // 1- 200 返回正确信息
        0   => '操作成功',

        // 200 以上是返回错误信息
        201 => '提交参数为空',
        202 => '创建目录失败',
        203 => '文件上传失败',
        204 => '文件上传移送失败',
        205 => '数据载入失败',
        206 => '服务器繁忙, 请稍候再试...',
        207 => '提交参数错误, 请确认操作',
        208 => '抱歉! 你没有删除角色的权限',
        209 => '抱歉! 这个角色还在使用',
        210 => '抱歉! 删除角色失败',
        211 => '添加角色失败',
        212 => '添加权限失败',
        213 => '修改权限失败',
        214 => '抱歉! 你没有删除权限操作的权限',
        215 => '抱歉! 删除权限失败',
        216 => '对不起，您现在还没获得该操作的权限!',
        217 => '抱歉! 您填写的数据表信息不存在',
        218 => '抱歉! 查询数据表信息出现错误',
        219 => '抱歉! 文件存在, 不能执行覆盖操作',
        220 => '查询数据不存在, 请确认操作',
        221 => '没有文件上传',
        222 => '删除数据不存在',
        223 => '生成权限失败',
        224 => '生成菜单失败',

        1001 => '添加数据存在错误',
        1002 => '修改的数据不存在',
        1003 => '修改数据存在错误',
        1004 => '删除数据失败',
    ],
];