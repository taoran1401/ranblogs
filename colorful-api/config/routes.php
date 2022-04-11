<?php

declare(strict_types=1);

use Hyperf\HttpServer\Router\Router;

Router::get('/test', 'App\Controller\IndexController@index');

/**
 * admin
 */
//login
Router::post('/api/admin/login', 'App\Controller\Admin\AdminUserController@login');
Router::addGroup('/api/admin', function () {
    //logout
    Router::post('/logout', 'App\Controller\Admin\AdminUserController@logout');
    //文件上传
    Router::post('/uploads', 'App\Controller\Admin\UploadController@index');
    Router::post('/upload/base64', 'App\Controller\Admin\UploadController@uploadBase64');
    //管理员管理
    Router::get('/admin/users', 'App\Controller\Admin\AdminUserController@index');
    Router::get('/admin/users/{id}', 'App\Controller\Admin\AdminUserController@show');
    Router::post('/admin/users', 'App\Controller\Admin\AdminUserController@store');
    Router::put('/admin/users/{id}', 'App\Controller\Admin\AdminUserController@update');
    Router::delete('/admin/users/{id}', 'App\Controller\Admin\AdminUserController@destroy');
    //获取当前管理员相关信息
    Router::get('/current/users', 'App\Controller\Admin\AdminUserController@getCurrentUser');
    //文章分类
    Router::get('/article/categorys', 'App\Controller\Admin\ArticleCategoryController@index');
    Router::get('/article/categorys/{id}', 'App\Controller\Admin\ArticleCategoryController@show');
    Router::post('/article/categorys', 'App\Controller\Admin\ArticleCategoryController@store');
    Router::put('/article/categorys/{id}', 'App\Controller\Admin\ArticleCategoryController@update');
    Router::delete('/article/categorys/{id}', 'App\Controller\Admin\ArticleCategoryController@destroy');
    //文章
    Router::get('/articles', 'App\Controller\Admin\ArticleController@index');
    Router::get('/articles/{id}', 'App\Controller\Admin\ArticleController@show');
    Router::post('/articles', 'App\Controller\Admin\ArticleController@store');
    Router::put('/articles/{id}', 'App\Controller\Admin\ArticleController@update');
    Router::delete('/articles/{id}', 'App\Controller\Admin\ArticleController@destroy');

    // 角色管理
    Router::get('/roles', 'App\Controller\Admin\RoleController@index');
    Router::get('/roles/{id}', 'App\Controller\Admin\RoleController@show');
    Router::post('/roles', 'App\Controller\Admin\RoleController@store');
    Router::put('/roles/{id}', 'App\Controller\Admin\RoleController@update');
    Router::delete('/roles/{id}', 'App\Controller\Admin\RoleController@destroy');

    // 权限管理
    Router::get('/permissions', 'App\Controller\Admin\PermissionController@index');
    Router::get('/permissions/{id}', 'App\Controller\Admin\PermissionController@show');
    Router::post('/permissions', 'App\Controller\Admin\PermissionController@store');
    Router::put('/permissions/{id}', 'App\Controller\Admin\PermissionController@update');
    Router::delete('/permissions/{id}', 'App\Controller\Admin\PermissionController@destroy');

    // 菜单管理
    Router::get('/menus', 'App\Controller\Admin\MenuController@index');
    Router::get('/menus/{id}', 'App\Controller\Admin\MenuController@show');
    Router::post('/menus', 'App\Controller\Admin\MenuController@store');
    Router::put('/menus/{id}', 'App\Controller\Admin\MenuController@update');
    Router::delete('/menus/{id}', 'App\Controller\Admin\MenuController@destroy');

    //网站配置
    Router::get('/configs', 'App\Controller\Admin\ConfigController@index');
    Router::post('/config/base', 'App\Controller\Admin\ConfigController@updateBase');
}, ['middleware' => [\App\Middleware\AdminAuthMiddleware::class, \App\Middleware\RBACMiddleware::class]]);

/**
 * api
 */
Router::addGroup('/api', function () {
    //文章
    Router::get('/articles', 'App\Controller\Api\ArticleController@index');
    Router::get('/articles/{id}', 'App\Controller\Api\ArticleController@show');
    //归档
    Router::get('/article/archives', 'App\Controller\Api\ArticleController@Archive');
    //文章标签
    Router::get('/article/tags', 'App\Controller\Api\ArticleTagController@index');
    //网站信息
    Router::get('/configs', 'App\Controller\Api\ConfigController@index');
    //关于
    Router::get('/about_us', 'App\Controller\Api\ConfigController@AboutUs');
    // 百度指数数据获取
    Router::post('/baidu/indexs', 'App\Controller\Api\RankController@baiduIndexInsert');
});