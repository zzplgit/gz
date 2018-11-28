<aside class="main-sidebar">

    <section class="sidebar">
        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => [
                    //['label' => '园所设置', 'icon' => 'align-left', 'url' => ['school/list'],],
                    ['label' => '园长设置', 'icon' => 'address-book', 'url' => ['headmaster/list'],],
                    ['label' => '学年设置', 'icon' => 'columns', 'url' => ['year/list']],
                    //['label' => '管理员设置', 'icon' => 'file-code-o', 'url' => ['admin/list']],
                ],
            ]
        ) ?>

    </section>

</aside>
