
<vd-setting-block-blog_post>
    <div class="form-group">
        <label class="control-label">{store.getLocal('blocks.blog_post.entry_title')}</label>
        <div class="fg-setting">
            <input type="text" class="form-control" name="title" value="{setting.global.title}" onChange={change}/>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label">{store.getLocal('blocks.blog_post.entry_layout')}</label>
        <div class="fg-setting">
            <input type="text" class="form-control" name="layout" value="{setting.global.layout}" onChange={change}/>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label">{store.getLocal('blocks.blog_post.entry_mode')}</label>
        <div class="fg-setting">
            <select class="form-control" name="mode" onChange={change}>
                <option value="{key}" selected="{key == setting.global.mode}" each="{value, key in store.getOptions('blocks.blog_post.modes')}">{value}</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label">{store.getLocal('blocks.blog_post.entry_limit')}</label>
        <div class="fg-setting">
            <input type="text" class="form-control" name="limit" value="{setting.global.limit}" onChange={change}/>
        </div>
    </div>
    <div class="form-group" hide={setting.global.mode != 'featured'}>
        <label class="control-label">{store.getLocal('blocks.blog_post.text_custom_products')}</label>
        <div class="fg-setting">
            <vd-autocomplete-well
                name="posts"
                value={setting.global.posts}
                options={setting.edit.posts}
                url={store.getOptions('blocks.blog_post.autocomplete_posts')}
                evchange={change}/>
        </div>
    </div>
    <!--<div class="form-group" hide={setting.global.mode != 'custom_categories'}>-->
        <!--<label class="control-label">{store.getLocal('blocks.products_grid.entry_categories')}</label>-->
        <!--<div class="fg-setting">-->
            <!--<vd-autocomplete-well -->
                <!--name="categories" -->
                <!--value={setting.global.categories} -->
                <!--options={setting.edit.categories}-->
                <!--url={store.getOptions('blocks.products_grid.autocomplete_categories')} -->
                <!--evchange={change}/>-->
        <!--</div>-->
    <!--</div>-->
    <!--<div class="form-group" hide={setting.global.mode == 'custom_products'}>-->
        <!--<label class="control-label">{store.getLocal('blocks.products_grid.entry_count_product')}</label>-->
        <!--<div class="fg-setting">-->
            <!--<input type="text" class="form-control" name="count_product" value="{setting.global.count_product}" onChange={change}/>-->
        <!--</div>-->
    <!--</div>-->
    <script>
        this.mixin({store:d_visual_designer})
        this.setting = this.opts.block.setting
        this.on('update', function(){
            this.setting = this.opts.block.setting
        })
        change(e){
            this.setting.global[e.target.name] = e.target.value
            this.store.dispatch('block/setting/fastUpdate', {designer_id: this.parent.designer_id, block_id: this.opts.block.id, setting: this.setting})
            this.update()
        }
    </script>
</vd-setting-block-blog_post>