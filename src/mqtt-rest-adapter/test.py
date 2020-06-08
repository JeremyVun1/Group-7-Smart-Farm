from spritz import Spritz
import binascii

spritz = Spritz()

K = bytearray('password', 'utf-8')
M = bytearray('Hello world!', 'utf-8')
C = spritz.encrypt(K, M)
print(C)

print(binascii.hexlify(C))

# Decryption
M = spritz.decrypt(K, C)
print(M)