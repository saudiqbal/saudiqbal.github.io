#!/usr/bin/bash

#Here is a sample custom api script.
#This file name is "dns_phpbind.sh"
#So, here must be a method   dns_phpbind_add()
#Which will be called by acme.sh to add the txt record to your api system.
#returns 0 means success, otherwise error.
#
#Author: Neilpang
#Report Bugs here: https://github.com/acmesh-official/acme.sh
#
########  Public functions #####################

# Please Read this guide first: https://github.com/acmesh-official/acme.sh/wiki/DNS-API-Dev-Guide

# Usage:
# export PHPBIND_PASSWORD="y6piHUklqGhZn6BhULmYraNhEfZKlSep"
# export PHPBIND_DNS_SERVER="https://example.com/acme.php"
# /usr/local/ssl/acme.sh/acme.sh --dns dns_phpbind --issue -d domain.tld

#Usage: dns_phpbind_add   _acme-challenge.www.example.com   "y6piHUklqGhZn6BhULmYraNhEfZKlSep"

dns_phpbind_add() {
  fulldomain=$1
  txtvalue=$2
  _info "Using phpbind"
  _debug fulldomain "$fulldomain"
  _debug txtvalue "$txtvalue"

  PHPBIND_PASSWORD="${PHPBIND_PASSWORD:-$(_readaccountconf_mutable PHPBIND_PASSWORD)}"
  PHPBIND_DNS_SERVER="${PHPBIND_DNS_SERVER:-$(_readaccountconf_mutable PHPBIND_DNS_SERVER)}"

  if [ -z "$PHPBIND_PASSWORD" ]; then
    PHPBIND_PASSWORD=""
    _err "You didn't specify the password yet."
    _err "Please specify the password and try again."
    return 1
  fi

  if [ -z "$PHPBIND_DNS_SERVER" ]; then
    PHPBIND_DNS_SERVER=""
    _err "You didn't specify the DNS server yet."
    _err "Please specify the DNS server and try again."
    return 1
  fi

  #save the credentials to the account conf file.
  _saveaccountconf_mutable PHPBIND_PASSWORD "$PHPBIND_PASSWORD"
  _saveaccountconf_mutable PHPBIND_DNS_SERVER "$PHPBIND_DNS_SERVER"

  uri="$PHPBIND_DNS_SERVER"
  data="?password=${PHPBIND_PASSWORD}&hostname=${fulldomain}&txt=${txtvalue}"
  result="$(_get "${uri}${data}")"
  _debug "Result: $result"

  if ! _startswith "$result" 'good'; then
    _err "Can't add $fulldomain"
    return 1
  fi

}

#Usage: fulldomain txtvalue
#Remove the txt record after validation.
dns_phpbind_rm() {
  fulldomain=$1
  txtvalue=$2
  _info "Using phpbind"
  _debug fulldomain "$fulldomain"
  _debug txtvalue "$txtvalue"

  PHPBIND_PASSWORD="${PHPBIND_PASSWORD:-$(_readaccountconf_mutable PHPBIND_PASSWORD)}"
  PHPBIND_DNS_SERVER="${PHPBIND_DNS_SERVER:-$(_readaccountconf_mutable PHPBIND_DNS_SERVER)}"

  uri="$PHPBIND_DNS_SERVER"
  data="?password=${PHPBIND_PASSWORD}&hostname=${fulldomain}&txt=${txtvalue}&action=delete"
  result="$(_get "${uri}${data}")"
  _debug "Result: $result"

  if ! _startswith "$result" 'good'; then
    _info "Can't remove $fulldomain"
  fi

}

####################  Private functions below ##################################
