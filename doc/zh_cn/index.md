# Sdds For Php 0.1.0

## 概要
    
Sdds For Php是一个依据SDDS规范用PHP写成的Sdds引擎。此程序即是依据SDDS规范对SOCKET的二进制通信数据包进行编码与解码的引擎。
      
关于SDDS规范，请参考：[SDDS规范](https://github.com/byteferry/sdds/doc/zh_cn/index.MD)
      
## 特性
    
* Sdds For Php支持SDDS规范要求的所有特性。同时支持一套程序多个协议。
* Sdds For Php支持以下数据类型的读写：
  
* 使用Sdds For Php可以大大简化你的二进制应用协议的编码与解杩的开发。
   
    
## 快速入门
    
    要开始使用Sdds Engine For php，你需要按照以下步骤
    
    * 通过composer安装Sdds Engine For php
```
    $ composer require byteferry/sdds 0.1.0
```         
    * 编写你的应用协议的方案（schema）
    要开始编写应用协议的方案，请参考SDDS规范的快速入门向导。
    
    * 调试你的应用协议的方案（schema）
    你可以通过http页面访问模式来调试你的应用协议的方案（schema），你可以借助dd函数输出调试信息。
    
    