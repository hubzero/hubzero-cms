    /**
    * o------------------------------------------------------------------------------o
    * | This file is part of the RGraph package - you can learn more at:             |
    * |                                                                              |
    * |                          http://www.rgraph.net                               |
    * |                                                                              |
    * | This package is licensed under the RGraph license. For all kinds of business |
    * | purposes there is a small one-time licensing fee to pay and for non          |
    * | commercial  purposes it is free to use. You can read the full license here:  |
    * |                                                                              |
    * |                      http://www.rgraph.net/LICENSE.txt                       |
    * o------------------------------------------------------------------------------o
    */
    
    if (typeof(RGraph) == 'undefined') RGraph = {};

    /**
    * The bar chart constructor
    * 
    * @param string canvas The canvas ID
    * @param min    integer The minimum value
    * @param max    integer The maximum value
    * @param value  integer The indicated value
    */
    RGraph.Meter = function (id, min, max, value)
    {
        // Get the canvas and context objects
        this.id                = id;
        this.canvas            = document.getElementById(id);
        this.context           = this.canvas.getContext ? this.canvas.getContext("2d") : null;
        this.canvas.__object__ = this;
        this.type              = 'meter';
        this.min               = min;
        this.max               = max;
        this.value             = value;
        this.centerx           = null;
        this.centery           = null;
        this.radius            = null;


        /**
        * Compatibility with older browsers
        */
        RGraph.OldBrowserCompat(this.context);


        // Various config type stuff
        this.properties = {
            'chart.gutter':                 25,
            'chart.linewidth':              2,
            'chart.border.color':           'black',
            'chart.text.font':              'Verdana',
            'chart.text.size':              10,
            'chart.text.color':             'black',
            'chart.title':                  '',
            'chart.title.vpos':             null,
            'chart.title.color':            'black',
            'chart.green.start':            ((this.max - this.min) * 0.35) + this.min,
            'chart.green.end':              this.max,
            'chart.green.color':            '#207A20',
            'chart.yellow.start':           ((this.max - this.min) * 0.1) + this.min,
            'chart.yellow.end':             ((this.max - this.min) * 0.35) + this.min,
            'chart.yellow.color':           '#D0AC41',
            'chart.red.start':              this.min,
            'chart.red.end':                ((this.max - this.min) * 0.1) + this.min,
            'chart.red.color':              '#9E1E1E',
            'chart.units.pre':              '',
            'chart.units.post':             '',
            'chart.labels.position':        'outside',
            'chart.contextmenu':            null,
            'chart.zoom.factor':            1.5,
            'chart.zoom.fade.in':           true,
            'chart.zoom.fade.out':          true,
            'chart.zoom.hdir':              'right',
            'chart.zoom.vdir':              'down',
            'chart.zoom.frames':            15,
            'chart.zoom.delay':             33,
            'chart.zoom.shadow':            true,
            'chart.zoom.mode':              'canvas',
            'chart.zoom.thumbnail.width':   75,
            'chart.zoom.thumbnail.height':  75,
            'chart.zoom.background':        true,
            'chart.annotatable':            false,
            'chart.annotate.color':         'black',
            'chart.shadow':                 false,
            'chart.shadow.color':           'rgba(0,0,0,0.5)',
            'chart.shadow.blur':            3,
            'chart.shadow.offsetx':         3,
            'chart.shadow.offsety':         3
        }


        // Check for support
        if (!this.canvas) {
            alert('[METER] No canvas support');
            return;
        }
        
        // Check the canvasText library has been included
        if (typeof(RGraph) == 'undefined') {
            alert('[METER] Fatal error: The common library does not appear to have been included');
        }
        
        /**
        * Constrain the value to be within the min and max
        */
        if (this.value > this.max) this.value = this.max;
        if (this.value < this.min) this.value = this.min;
    }


    /**
    * A setter
    * 
    * @param name  string The name of the property to set
    * @param value mixed  The value of the property
    */
    RGraph.Meter.prototype.Set = function (name, value)
    {
        this.properties[name.toLowerCase()] = value;
    }


    /**
    * A getter
    * 
    * @param name  string The name of the property to get
    */
    RGraph.Meter.prototype.Get = function (name)
    {
        return this.properties[name];
    }


    /**
    * The function you call to draw the bar chart
    */
    RGraph.Meter.prototype.Draw = function ()
    {
        // Cache the gutter as a object variable because it's used a lot
        this.gutter  = this.Get('chart.gutter');

        this.centerx = this.canvas.width / 2;
        this.centery = this.canvas.height - this.gutter;
        this.radius  = Math.min(this.canvas.width - (2 * this.gutter), this.canvas.height - (2 * this.gutter));

        this.DrawBackground();
        this.DrawNeedle();
        this.DrawLabels();
        
        /**
        * Draw the title
        */
        RGraph.DrawTitle(this.canvas, this.Get('chart.title'), this.gutter);

        /**
        * Setup the context menu if required
        */
        RGraph.ShowContext(this);
        
        /**
        * If the canvas is annotatable, do install the event handlers
        */
        RGraph.Annotate(this);
        
        /**
        * This bit shows the mini zoom window if requested
        */
        RGraph.ShowZoomWindow(this);
        
        /**
        * For MSIE only, to cover the spurious lower ends of the circle
        */
        if (document.all) {
            // Cover the left tail
            this.context.beginPath();
            this.context.moveTo(this.gutter, this.canvas.height - this.gutter);
            this.context.fillStyle = 'white';
            this.context.fillRect(this.centerx - this.radius - 5, this.canvas.height - this.gutter + 1, 10, this.gutter);
            this.context.fill();

            // Cover the right tail
            this.context.beginPath();
            this.context.moveTo(this.canvas.width - this.gutter, this.canvas.height - this.gutter);
            this.context.fillStyle = 'white';
            this.context.fillRect(this.centerx + this.radius - 5, this.canvas.height - this.gutter + 1, 10, this.gutter);
            this.context.fill();
        }
    }


    /**
    * Draws the background of the chart
    */
    RGraph.Meter.prototype.DrawBackground = function ()
    {
        // Draw the shadow
        if (this.Get('chart.shadow')) {
            this.context.beginPath();
                this.context.fillStyle = 'white';
                this.context.shadowColor   = this.Get('chart.shadow.color');
                this.context.shadowBlur    = this.Get('chart.shadow.blur');
                this.context.shadowOffsetX = this.Get('chart.shadow.offsetx');
                this.context.shadowOffsetY = this.Get('chart.shadow.offsety');
                
                this.context.arc(this.centerx, this.centery, this.radius, 3.14, 6.28, false);
                //this.context.arc(this.centerx, this.centery, , 0, 6.28, false);
            this.context.fill();


            this.context.beginPath();
                var r = (this.radius * 0.06) > 40 ? 40 : (this.radius * 0.06);
                this.context.arc(this.centerx, this.centery, r, 0, 6.28, 0);
            this.context.fill();

            RGraph.NoShadow(this);
        }

        // First, draw the grey tickmarks
        this.context.beginPath();
        this.context.strokeStyle = '#bbb'
        for (var i=0.17; i<3.14; i+=(0.13/3)) {
            this.context.arc(this.centerx, this.centery, this.radius, 3.1415927 + i, 3.1415927 + i, 0);
            this.context.lineTo(this.centerx, this.centery);
        }
            this.context.stroke();

        // First, draw the tickmarks
        for (var i=0.17; i<3.14; i+=0.13) {
            this.context.beginPath();
            this.context.strokeStyle = this.Get('chart.border.color');
            this.context.arc(this.centerx, this.centery, this.radius, 3.1415927 + i, 3.1415927 + i, 0);
            this.context.lineTo(this.centerx, this.centery)
            this.context.stroke();
        }
        
        // Draw the white circle that makes the tickmarks
        this.context.beginPath();
        this.context.fillStyle = 'white'
        this.context.arc(this.centerx, this.centery, this.radius - 4, 3.1415927, 6.28, false);
        this.context.closePath();
        this.context.fill();

        // Draw the green area
        this.context.strokeStyle = this.Get('chart.green.color');
        this.context.fillStyle = this.Get('chart.green.color');
        this.context.beginPath();
        this.context.arc(this.centerx,this.centery,this.radius * 0.85,(((this.Get('chart.green.start') - this.min) / (this.max - this.min)) * 3.1415927) + 3.1415927,(((this.Get('chart.green.end') - this.min) / (this.max - this.min)) * 3.1415927) + 3.1415927,false);
        this.context.lineTo(this.centerx, this.centery);
        this.context.closePath();
        this.context.stroke();
        this.context.fill();
        
        // Draw the yellow area
        this.context.strokeStyle = this.Get('chart.yellow.color');
        this.context.fillStyle = this.Get('chart.yellow.color');
        this.context.beginPath();        this.context.arc(this.centerx,this.centery,this.radius * 0.85,(((this.Get('chart.yellow.start') - this.min) / (this.max - this.min)) * 3.1415927) + 3.1415927,(((this.Get('chart.yellow.end') - this.min) / (this.max - this.min)) * 3.1415927) + 3.1415927,false)
        this.context.lineTo(this.centerx, this.centery);
        this.context.closePath();
        this.context.stroke();
        this.context.fill();
        
        // Draw the yellow area
        this.context.strokeStyle = this.Get('chart.red.color');
        this.context.fillStyle = this.Get('chart.red.color');
        this.context.beginPath();
        this.context.arc(this.centerx,this.centery,this.radius * 0.85,(((this.Get('chart.red.start') - this.min) / (this.max - this.min)) * 3.1415927) + 3.1415927,(((this.Get('chart.red.end') - this.min) / (this.max - this.min)) * 3.1415927) + 3.1415927,false);
        this.context.lineTo(this.centerx, this.centery);
        this.context.closePath();
        this.context.stroke();
        this.context.fill();

        // Draw the outline
        this.context.strokeStyle = this.Get('chart.border.color');
        this.context.lineWidth   = this.Get('chart.linewidth');

        this.context.beginPath();
        this.context.moveTo(this.centerx, this.centery);
        this.context.arc(this.centerx, this.centery, this.radius, 3.1415927, 6.2831854, false);
        this.context.closePath();

        this.context.stroke();
        
        // Reset the linewidth back to 1
        this.context.lineWidth = 1;
    }


    /**
    * Draws the pointer
    */
    RGraph.Meter.prototype.DrawNeedle = function ()
    {
        // First draw the circle at the bottom
        this.context.fillStyle = 'black';
        this.context.lineWidth = this.radius >= 200 ? 7 : 3;
        this.context.lineCap = 'round';

        // Now, draw the pointer
        this.context.beginPath();
        this.context.strokeStyle = 'black';
        var a = (((this.value - this.min) / (this.max - this.min)) * 3.14) + 3.14;
        this.context.arc(this.centerx, this.centery, this.radius * 0.7, a, a + (document.all ? 0.001 : 0), false);
        this.context.lineTo(this.centerx, this.centery);
        this.context.stroke();

        // Draw the center circle
        var r = (this.radius * 0.06) > 40 ? 40 : (this.radius * 0.06);

        this.context.beginPath();
        this.context.arc(this.centerx, this.centery, r, 0, 6.28, 0);
        this.context.fill();
        
        // Draw the centre bit of the circle
        this.context.fillStyle = 'white';
        this.context.beginPath();
        this.context.arc(this.centerx, this.centery, r - 2, 0, 6.28, 0);
        this.context.fill();
    }


    /**
    * Draws the labels
    */
    RGraph.Meter.prototype.DrawLabels = function ()
    {
        var context    = this.context;
        var radius     = this.radius;
        var text_size  = this.Get('chart.text.size');
        var text_font  = this.Get('chart.text.font');
        var units_post = this.Get('chart.units.post');
        var units_pre  = this.Get('chart.units.pre');
        var centerx    = this.centerx;
        var centery    = this.centery;
        var min        = this.min;
        var max        = this.max;

        context.fillStyle = this.Get('chart.text.color');
        context.lineWidth = 1;

        context.beginPath();

        if (this.Get('chart.labels.position') == 'inside') {

            RGraph.Text(context, text_font, text_size, centerx - radius + (0.075 * radius), centery - 10, units_pre + min + units_post, 'center', 'left', false, 270);
            RGraph.Text(context,text_font,text_size,centerx - (Math.cos(0.62819) * (radius - (0.085 * radius)) ),centery - (Math.sin(0.682819) * (radius - (0.085 * radius)) ),units_pre + (((max - min) / 5) + min) + units_post,'center','center', false, 306);
            RGraph.Text(context, text_font, text_size,centerx - (Math.cos(1.2566) * (radius - (0.085 * radius)) ),centery - (Math.sin(1.2566) * (radius - (0.0785 * radius)) ),units_pre + (((max - min) * 0.4) + min) + units_post,'center', 'center', false, 342);
            RGraph.Text(context,text_font,text_size,centerx - (Math.cos(1.88495562) * (radius - (0.075 * radius)) ),centery - (Math.sin(1.88495562) * (radius - (0.075 * radius)) ),units_pre + (((max - min)* 0.6) + min) + units_post,'center','center', false, 18);
            RGraph.Text(context,text_font,text_size,centerx - (Math.cos(2.51327416) * (radius - (0.075 * radius)) ),centery - (Math.sin(2.51327416) * (radius - (0.075 * radius)) ), units_pre + (((max - min)* 0.8) + min) + units_post,'center','center', false, 54);
            RGraph.Text(context, text_font, text_size,centerx + radius - (0.075 * radius),centery - 10,units_pre + (max) + units_post, 'center', 'right', false, 90);

        } else {
            
            RGraph.Text(context, text_font, text_size,centerx - radius - (0.075 * radius),centery,units_pre + min + units_post, 'center', 'center', false, 270);
            RGraph.Text(context,text_font,text_size,centerx - (Math.cos(0.62819) * (radius + (0.085 * radius)) ),centery - (Math.sin(0.682819) * (radius + (0.085 * radius)) ),units_pre + (((max - min) / 5) + min) + units_post,'center','center', false, 306);
            RGraph.Text(context, text_font, text_size,centerx - (Math.cos(1.2566) * (radius + (0.085 * radius)) ),centery - (Math.sin(1.2566) * (radius + (0.0785 * radius)) ),units_pre + (((max - min) * 0.4) + min) + units_post,'center', 'center', false, 342);
            RGraph.Text(context,text_font,text_size,centerx - (Math.cos(1.88495562) * (radius + (0.075 * radius)) ),centery - (Math.sin(1.88495562) * (radius + (0.075 * radius)) ),units_pre + (((max - min)* 0.6) + min) + units_post,'center','center', false, 18);
            RGraph.Text(context,text_font,text_size,centerx - (Math.cos(2.51327416) * (radius + (0.075 * radius)) ),centery - (Math.sin(2.51327416) * (radius + (0.075 * radius)) ),units_pre + (((max - min)* 0.8) + min) + units_post,'center','center', false, 54);
            RGraph.Text(context, text_font, text_size,centerx + radius + (0.075 * radius),centery,units_pre + (max) + units_post, 'center', 'center', false, 90);
        }

        context.fill();
        context.stroke();
    }
