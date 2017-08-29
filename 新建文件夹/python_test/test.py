# print ('hello, world');


# classmates = ['Michael', 'Bob', 'Tracy'];

# print (classmates[0]);
# print (classmates[1]);
# print (classmates[2]);

# age = 20
# if age >= 18:
#     print('your age is', age)
#     print('adult')

# names = ['Michael', 'Bob', 'Tracy']
# for name in names:
#     print(name)

#sum = 0
#for x in [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]:
#    sum = sum + x
#print(sum)

import urllib.request

#response = urllib.request.urlopen('http://www.baidu.com/')
#html = response.read()
#print (html)

#coding:utf-8

f = open("demo_1.html",'w')
message = """
<html>
<head></head>
<body>
<p>Hello,World!</p>
<p>demo不错</p>
</body>
</html>"""

f.write(message)
f.close()
