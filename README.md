**17MON LookingGlass**

LookingGlass 在维基百科上的解释为“Looking Glass servers are computers on the Internet running one of a variety of publicly available Looking Glass software implementations. A Looking Glass server (or lg server) is accessed remotely for the purpose of viewing routing information. Essentially, the server acts as a limited, read-only portal to routers of whatever organization is running the lg server. Typically, publicly accessible looking glass servers are run by Internet service providers (ISPs) or Internet Exchange Points (IXPs).” - [http://en.wikipedia.org/wiki/Looking_Glass_servers](http://en.wikipedia.org/wiki/Looking_Glass_servers "WIKIPEDIA")

这样的服务，对于网络运维人员和相关的技术人员相当有用，在国外，大致跨国运营商，小致做云主机或者 VPS 的公司，大体都会有此类服务，用于让你更好更方便的了解网络情况。

但是，在国内，几乎没有看到有假设此类服务的，无论规模与否。

我看过几个开源代码，我觉得还有改进余地，比如结果的逐行输出，比如 traceroute 的格式化输出，这个在我们做 17MON 的经历中，感觉还是非常有用的。

所以我们也希望基于 17MON 的经验，做一个 Looking Glass 服务出来，希望大家可以更好的来使用这个服务。

**演示地址：**

1、[http://tokyo.lg.17mon.cn](http://tokyo.lg.17mon.cn "日本东京") 日本东京

2、[http://fremont.lg.17mon.cn](http://fremont.lg.17mon.cn "美国美国费利蒙") 美国美国费利蒙

3、[http://moscow.lg.17mon.cn](http://moscow.lg.17mon.cn "俄罗斯莫斯科") 俄罗斯莫斯科

**有几点说明：**

1、配置方式，请见 config.php，另外请做好该服务器的相关安全工作。

2、这里面包含的 IP 库基于 17MON 的公开版。您如果使用的话，需要定期自行更新，目前是每月初更新。

3、结果的逐行输出，需要 webserver 支持，建议使用 Nginx 1.5.6 版本以上，即可支持逐行输出，设置例子请见 nginx.conf。

**相关链接：**

1、[IP地址库下载](http://tool.17mon.cn/ipdb.html "IP 归属地数据库")

2、[17MON网络工具集](http://tool.17mon.cn/ "17MON网络工具集") 目前由我和同事业余在维护的，全球有 100 多个监测点可供使用，tracert 功能尤其强大和准确，是你 ping 和 tracert 的好帮手。