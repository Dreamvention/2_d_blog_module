<vd-block-blog_post>
    <div class="bm">
        <div class="h2" if="{getState().setting.global.title}">{getState().setting.global.title}</div>
        <div class="bm-posts">
            <div class="bm-grid bm-grid">
                <virtual each="{row in getState().rows}">
                    <div class="row">
                        <virtual each="{post in row}">
                            <div class="bm-grid-item col-sm-{post.col}"
                                 animate="{(post.animate)?post.animate:null}">
                                <div class="bm-grid-item-body animated">
                                    <raw html="{post.partial}"></raw>
                                </div>
                            </div>
                        </virtual>
                    </div>
                </virtual>
            </div>
        </div>
    </div>
    <script>
		this.mixin(new vd_block(this));
		this.initState({
			rows: []
		});

		this.initRows = function () {

			var rows = this.getState().rows;
			var posts = this.getState().setting.user.posts;
			var layout = this.getState().setting.global.layout;
			rows = [];
			var layout_array = layout.toString().split('-');
			var total = _.reduce(layout_array, function (el, num) {
				return +el + +num;
			});
			var new_posts = {};
			var sheme = [];
			var sub_total = 0;
			//dima make this ))
            // make a sheme like {0:0.25,1:0.5,2:0.75,3:1}
            // for search element position in scheme
            // init this array
			for (var row in layout_array) {
				sub_total += +layout_array[row];
				sheme.push(sub_total / total);
				new_posts[row] = [];
			}
			//cout schemas for valid adding more than limit posts
			var cout_shemas = 0;
			for (var i in posts) {
				var post_i = ((+i + 1) % +total) / +total;
				var row_id = cout_shemas;
				if (post_i == 0) { //last one i in row
					row_id = sheme.length - 1;
				} else {
					// find position in a shema for post
					for (var j in sheme) {
						if (post_i <= sheme[j]) {
							row_id = j;
							break;
						}
					}
				}
				new_post = JSON.parse(JSON.stringify(posts[i]));
				new_post.col = Math.round(12 / layout_array[row_id]);
				new_posts[+row_id + cout_shemas * (sheme.length)].push(new_post);
				//last one i in row need to increace shema_count
				if (post_i == 0) {
					cout_shemas++;
					for (var row in layout_array) {
						new_posts[+row + +cout_shemas * (sheme.length)] = [];
					}
				}

			}
			this.setState({rows: new_posts});
		};
		this.on('update', function () {
			this.initRows();
		});

		this.on('mount', function () {

			this.initRows();
			this.update();
		});

    </script>
</vd-block-blog_post>

