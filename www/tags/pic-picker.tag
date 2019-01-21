<pic-picker>
    <div class=section>Pictures</div>
    <div class=thumbs ref=picker></div>
    <style>
        :scope {
            position: absolute;
            top: 0px;
            left: 0px;
            width: 100%;
            height: 100%;
            overflow-y: auto;
        }

        .section {
            font-size: 20px;
            text-align: center;
        }

        .thumbs {
            text-align: center;
        }
    </style>
    <script>
        this.on("mount", () => {
            require(["text!../api/info", "riot-tag!pic-thumb"], info => {
                info = JSON.parse(info);
                for (let el in info.list) {
                    var d = document.createElement("div");
                    this.refs.picker.appendChild(d);
                    riot.mount(d, 'pic-thumb', {name: info.list[el]});
                }
            });
        });
    </script>
</pic-picker>