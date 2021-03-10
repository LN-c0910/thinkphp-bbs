<?php
Route::group('admin',function (){
    Route::get('/','index/index')->name('admin/index/index');
    Route::get('/logout','index/logout')->name('admin/index/logout');
    Route::post('/login','index/login')->name('admin/index/login');
    Route::group(['middleware'=>['CheckLogin:1']],function (){
        Route::get('/main','index/main')->name('admin/index/main');
        Route::get('/main/section_manager','index/sectionManager')->name('admin/index/sectionManager');
        Route::get('/main/section_add','index/section_add')->name('admin/index/section_add');
        Route::post('main/section_add','uploadSetionImg')->name('uploadSetionImg');
        Route::get('/main/section_edit/<id>','index/section_edit')->name('admin/index/section_edit');
        Route::post('/main/section_edit/<id>','section/section_edit')->name('admin/section/section_edit');
        Route::get('/main/topic_manager','index/topicManager')->name('admin/index/topicManager');
        Route::get('/main/check_topic','index/checkTopicManager')->name('admin/index/checkTopicManager');
        Route::get('/main/trash_topic','index/trashTopicManager')->name('admin/index/trashTopicManager');
        Route::get('/main/topic/approve/<tid>','topic/approveTopic')->name('admin/topic/approveTopic');
        Route::delete('/main/topic/destory/<tid>','topic/destroyTopic')->name('admin/topic/destroyTopic');
        Route::post('/main/topic/restore/<tid>','topic/restoreTopic')->name('admin/topic/destroyTopic');
        Route::post('/main/update_topic_status/<tid>','topic/updateStatusByTid')->name('admin/topic/updateStatusByTid');
        Route::get('/main/update_topic_section/<tid>/<tsid>','topic/updateTopicSection')->name('admin/topic/updateTopicSection');
        Route::get('/main/topic_add','index/topic_add')->name('admin/index/topic_add');        Route::get('/main/topic_add','index/topic_add')->name('admin/index/topic_add');
        Route::get('/main/reply_manager','index/replyManager')->name('admin/index/replyManager');
        Route::get('/main/reply/view/<id>','index/viewReply')->name('admin/index/viewReply');
        Route::delete('/main/reply/delete/<id>','reply/delete')->name('admin/reply/delete');
        Route::put('/main/reply/restore/<id>','reply/restore')->name('admin/reply/restore');
        Route::get('/main/report_manager','index/reportManager')->name('admin/index/reportManager');
        Route::post('/main/report/feedback/<id>','report/feedback')->name('admin/report/feedback');
        Route::get('/main/user_manager','index/userList')->name('admin/index/userList');
        Route::get('/main/user/view/<id>','index/userView')->name('admin/index/userView');
        Route::post('/main/user/stop/<id>','user/stopUser')->name('admin/user/stopUser');
        Route::post('/main/user/start/<id>','user/startUser')->name('admin/user/startUser');
        Route::get('/main/user/add','index/userAdd')->name('admin/index/userAdd');
        Route::resource('user','user');
        Route::resource('topic','topic');
        Route::resource('section','section');
    });

})->prefix('admin/');
