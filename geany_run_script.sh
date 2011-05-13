#!/bin/sh

rm $0

php "contact-form-7-datepicker.php"

echo "

------------------
(program exited with code: $?)" 		


echo "Press return to continue"
#to be more compatible with shells like dash
dummy_var=""
read dummy_var
