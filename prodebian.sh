#!/bin/bash
# This script only requires "grep" and "coreutils"
# it will run every prodebian scripts below separately
# script can be bash, python, perl, awk, etc...

# get the name of this script and the current working dir
script=`pwd`/$0 # get the path of this script
savewd="`pwd`"

# create a temporary working directory
wdir=/tmp/prodebian.process$$
mkdir $wdir

# define the splitting pattern
pattern="#############_PRODEBIAN_SCRIPT_"

# be sure grep is installed
which grep 2>&1 >/dev/null || apt-get install grep

# how many scripts is there here ?
nbscripts=$((`grep -c "$pattern" $script`-2))

# initialize the log
log=/tmp/prodebian_install_log`date +%s`.txt
date -R > $log

i=1; # repeat for each script
while [ $i -le $nbscripts ]; do
  # extract the script number $i
  csplit -s -f $wdir/current $script %$pattern$i%+1 /$pattern/
  # execute the script number $i and write in the log
  cd $wdir
  cat current00 | tr -d '\r' > current00.new # this is a basic dos2unix
  mv current00.new current00
  chmod +x current00
  grep $pattern$i $script | tee -a $log
  ./current00 2>&1 | tee -a $log
  # delete the script
  rm -f current00
  cd $savewd
  i=$(($i+1))
done

# delete the working directory
rm -rf $wdir

# return where we come from
cd $savewd

# advertise for the log
echo "${pattern}_INSTALLATION FINISHED"
echo
echo "The log file for this installation is $log"

exit


