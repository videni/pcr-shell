# 数据导入步骤

1. 从Excel中导入数据到sqlite

2. 再将数据从sqlite中导入目标数据库


# 使用方法

1. 安装依赖库
    composer install
    
2. 导入到sqlite数据库中    
 ./Resources/bin/pcr pcr:sqlite
3. 现在，在var目录，你能找到pcr.sqlite文件。

# 下一步

* [X] 分装从Excel中导入数据到sqlite

* [X] 分装从sqlite中，获取省市区操作

* [X] 分装导入数据到目标数据库操作
