# xhprof2flamegraph

🎨将xhprof产生的数据转为可以生成flame graph火焰图的格式并生成火焰图

## 安装

```
composer require sy-records/xhprof2flamegraph
```

## 使用

`vendor/bin`目录下有两个可执行文件，执行命令
```shell
./vendor/bin/xhprof2flamegraph -f ./test.xhprof | ./vendor/bin/flamegraph.pl > out.svg
```

> 替换对应的路径信息
>
> -f 为指定的`xhprof`日志文件，<del>`json`格式 （如果日志文件为`serialize`序列化后的数据，请修改`src/Command/Command.php`文件中的第`40`行）</del>，自动处理`json`和`serialize`格式的数据

## 帮助

* xhprof2flamegraph -h
```shell
         _                               __   ____     __ 
 __  __ | |__    _ __    _ __    ___    / _| |___ \   / _|
 \ \/ / | '_ \  | '_ \  | '__|  / _ \  | |_    __) | | |_ 
  >  <  | | | | | |_) | | |    | (_) | |  _|  / __/  |  _|
 /_/\_\ |_| |_| | .__/  |_|     \___/  |_|   |_____| |_|  
                |_|                                       
                
Usage:
    xhprof2flamegraph [-h, --help] [-f, --profile] [--metrics] 
Options:
    -h, --help      show help
    -f, --profile   file path of xhprof profile data
    --metrics       select target metrics (ect/ewt/ecpu/emu/epmu) 
```

* flamegraph.pl -help
```shell
USAGE: ./flamegraph.pl [options] infile > outfile.svg

        --title TEXT     # change title text
        --subtitle TEXT  # second level title (optional)
        --width NUM      # width of image (default 1200)
        --height NUM     # height of each frame (default 16)
        --minwidth NUM   # omit smaller functions (default 0.1 pixels)
        --fonttype FONT  # font type (default "Verdana")
        --fontsize NUM   # font size (default 12)
        --countname TEXT # count type label (default "samples")
        --nametype TEXT  # name type label (default "Function:")
        --colors PALETTE # set color palette. choices are: hot (default), mem,
                         # io, wakeup, chain, java, js, perl, red, green, blue,
                         # aqua, yellow, purple, orange
        --hash           # colors are keyed by function name hash
        --cp             # use consistent palette (palette.map)
        --reverse        # generate stack-reversed flame graph
        --inverted       # icicle graph
        --negate         # switch differential hues (blue<->red)
        --notes TEXT     # add notes comment in SVG (for debugging)
        --help           # this message

        eg,
        ./flamegraph.pl --title="Flame Graph: malloc()" trace.txt > graph.svg
```
