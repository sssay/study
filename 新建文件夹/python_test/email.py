import smtplib    
from email.mime.text import MIMEText    
from email.header import Header    

msg=email.mime.multipart.MIMEMultipart()  
msg['from']='1526143316@qq.com'  
msg['to']='996862564@qq.com'  
msg['subject']='test'  
content=''''' 
    你好， 
            这是一封自动发送的邮件。 
 
        www.ustchacker.com 
'''  
txt=email.mime.text.MIMEText(content)  
msg.attach(txt)  
  
smtp=smtplib  
smtp=smtplib.SMTP()  
smtp.connect('smtp.tom.com','25')  
smtp.login('1526143316@qq.com','yang1992')  
smtp.sendmail('1526143316@qq.com','996862564@qq.com',str(msg))  
smtp.quit() 
