#!/bin/bash

# get the name of this script and the current working dir
script=$0 # get the name of this script
savewd="`pwd`"

# create a temporary working directory
wdir=/tmp/prodebian.process$$
mkdir $wdir

# define the splitting pattern
pattern="############################_PRODEBIAN_SCRIPT_"

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
  chmod +x current00
  echo $pattern$i | tee -a $log
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

exit


