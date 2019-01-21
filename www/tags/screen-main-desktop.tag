<screen-main-desktop>
    <div class=big-header>
        <div class=site-name ref=siteName>Robot Arm</div>
        <div class=site-author>
            by David O'Riva
        </div>
    </div>
    <div class=main-picker>
        <pic-picker></pic-picker>
    </div>
    <div class=main-window ref=info>
        <p>
            Robot arm build, January 2019
        </p>
    </div>
    <div class=main-window ref=viewer>
        <pic-viewer></pic-viewer>
    </div>
    <style>
        :scope {
            position: absolute;
            left: 0px;
            top: 0px;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background: #222;
            color: #ddd;
        }

        .big-header {
            position: absolute;
            left: 0px;
            top: 0px;
            height: 10%;
            width: 100%;
        }
        .main-picker {
            position: absolute;
            left: 0px;
            top: 10%;
            width: 250px;
            bottom: 0px;
        }
        .main-window {
            position: absolute;
            left: 250px;
            top: 10%;
            right: 0px;
            bottom: 0px;
        }

        .site-name {
            display: inline-block;
            vertical-align: baseline;
            margin-left: 20px;
        }
        .site-author {
            margin-left: 5px;
            display: inline-block;
            vertical-align: baseline;
        }
    </style>
    <script>
        this.on("mount", () => {
            this.refs.viewer.style.display = "none";
            var h = this.refs.siteName.parentNode.clientHeight;
            this.refs.siteName.style.fontSize = Math.floor(h * .9) + "px";
        });
        this.showImage = image => {
            this.refs.info.style.display = "none";
            this.refs.viewer.style.display = "block";
            this.opts.view = image;
            this.update();
        }
        window.mainScreen = this;
    </script>
</screen-main-desktop>