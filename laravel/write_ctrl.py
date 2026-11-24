import sys  
path = sys.argv[1]  
content = open(sys.argv[2]).read()  
open(path,'w').write(content)  
print('OK') 
