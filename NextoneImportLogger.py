#!/usr/bin/python
#hello bhai
import sys, optparse
import MySQLdb
import datetime
import os
import time,string
#import pyinotify
from warnings import filterwarnings
from time import sleep

LOGGER_PATH="/tmp/aug_22"
################# Required Functions ##########################
TableName="test"
NT1_IPAddress="38.76.68.133"

CDR_TABLE_FIELDS = ['start_time1', 'start_time2', 'call_duration1', 'call_source', 'call_source_q931sig_port', 'call_dest', 'BLANK1', 'call_source_custid', 'called_party_on_dest', 'called_party_from_src', 'call_type', 'BLANK2', 'disconnect_error_type', 'call_error1', 'call_error2', 'BLANK3', 'BLANK4', 'ani', 'BLANK5', 'BLANK6', 'BLANK7', 'cdr_seq_no', 'BLANK8', 'callid', 'call_hold_time', 'call_source_regid', 'call_source_uport', 'call_dest_regid', 'call_dest_uport', 'isdn_cause_code', 'called_party_after_src_calling_plan', 'call_error_dest1', 'call_error_dest2', 'call_error_event_str', 'new_ani', 'call_duration2', 'incoming_leg_callid', 'protocol', 'cdr_type', 'hunting_attempts', 'caller_trunk_group', 'call_pdd', 'h323_dest_ras_error', 'h323_dest_h225_error', 'sip_dest_respcode', 'dest_trunk_group', 'call_duration_fractional', 'timezone', 'msw_name', 'called_party_after_transit_route', 'called_party_on_dest_num_type', 'called_party_from_src_num_type', 'call_source_realm_name', 'call_dest_realm_name', 'call_dest_crname', 'call_dest_custid', 'call_zone_data', 'calling_party_on_dest_num_type', 'calling_party_from_src_num_type', 'original_isdn_cause_code', 'packets_received_on_src_leg', 'packets_lost_on_src_leg', 'packets_discarded_on_src_leg', 'pdv_on_src_leg', 'codec_on_src_leg', 'latency_on_src_leg', 'rfactor_on_src_leg', 'packets_received_on_dest_leg', 'packets_lost_on_dest_leg', 'packets_discarded_on_dest_leg', 'pdv_on_dest_leg', 'codec_on_dest_leg', 'latency_on_dest_leg', 'rfactor_on_dest_leg', 'sip_src_respcode', 'peer_protocol', 'src_private_ip', 'dest_private_ip', 'src_igrp_name', 'dest_igrp_name', 'diversion_info', 'custom_contact_tag', 'e911_call', 'reserved1', 'reserved2', 'call_release_source', 'hunt_attempts_including_LCF_tries', 'call_gapping_error', 'error_code_in_reason_header', 'ocl_object_type', 'ocl_object_id_dtn_regid_realmname', 'ocl_object_id_dtnrealm_uport', 'ocl_policy_name', 'src_private_port', 'dest_private_port', 'src_realm_media_ip', 'src_realm_media_port', 'dest_realm_media_ip', 'dest_realm_media_port', 'FileName', 'PDD', 'connecttime', 'disconnecttime']




def BuildQuintumTimeStamp(idatetime):
    datetime = idatetime.replace(' ','').replace('-','').replace(':','') # nextonee time 2010-09-15 00:01:01 => quintum yyyymmddss
    return (datetime)

def GetSeconds (intime):
    time = intime.split(":")
    return(int(time[0])*3600+int(time[1])*60+int(time[2]))

def GetOffsetTime(StartDateTime,timeoffset):
    #Start with Startdate, add holdtime ,
    #if we cross over midnight, we need calculate no other place in the
    #nextone cdr can indicate the date change
    datetime = StartDateTime.split(" ")
    time = datetime[1].split(":")
    sec = int(time[2])+ timeoffset
    min = int(time[1])+(sec / 60)
    sec = sec % 60
    hou = int(time[0])+(min/60)
    min = min % 60
    date = datetime[0].split("-")
    day = int(date[2])  
    mon = int(date[1])
    yer = int(date[0])
    if(hou >= 24):     #need move to next day
        day = day + (hou/24) 
        hou = hou % 24
        if(day > 28) and (mon==2): #note for feb we only consider 28 days,the cdr duration is get from nextone, so we asssuem the cdr still can be correct
            mon = mon + (day/28)
            day = day % 28
        elif(day > 31 ):  #if day great than 31, we need change month for sure
            mon = mon + (day/31)
            day = day % 31
        elif (day > 30):
             if (mon==4) or (mon==6) or (mon==9) or (mon==11):
                 mon = mon + (day/30)
                 day = day % 30
    if(mon>12):
        mon = 1+yer
        mon = 1
    odatetime = "%02d%02d%02d%02d%02d%02d" %(yer,mon,day,hou,min,sec)
    return(odatetime)

def ProcessNextoneFile(FileName) :
    inp = None
    try:
        inp = open(FileName,"r")
    except IOError:
        print "There was an error reading : ", FileName
        return
    try:
        print "....Importing file :",FileName
        countins=1
        cursor = conn.cursor ()
	for line in inp.readlines():
	    data = line.split(";")            
            Qrecord = []
            for i in CDR_TABLE_FIELDS:
                Qrecord.append(None)

            ######build the fields like CDR table
            data.insert(0, "dummy")
            Qrecord[0] = data[1]
            Qrecord[1] = data[2]
            Qrecord[2] = data[3]
            Qrecord[3] = data[4]
            Qrecord[4] = data[5]
            Qrecord[5] = data[6]
            Qrecord[6] = data[7]
            Qrecord[7] = data[8]
            Qrecord[8] = data[9]
            Qrecord[9] = data[10]
            Qrecord[10] = data[11]
            Qrecord[11] = data[12]
            Qrecord[12] = data[13]
            Qrecord[13] = data[14]
            Qrecord[14] = data[15]
            Qrecord[15] = data[16]
            Qrecord[16] = data[17]
            Qrecord[17] = data[18]
            Qrecord[18] = data[19]
            Qrecord[19] = data[20]
            Qrecord[20] = data[21]
            Qrecord[21] = data[22]
            Qrecord[22] = data[23]
            Qrecord[23] = data[24]
            Qrecord[24] = data[25]
            Qrecord[25] = data[26]
            Qrecord[26] = data[27]
            Qrecord[27] = data[28]
            Qrecord[28] = data[29]
            Qrecord[29] = data[30]
            Qrecord[30] = data[31]
            Qrecord[31] = data[32]
            Qrecord[32] = data[33]
            Qrecord[33] = data[34]
            Qrecord[34] = data[35]
            Qrecord[35] = data[36]
            Qrecord[36] = data[37]
            Qrecord[37] = data[38]
            Qrecord[38] = data[39]
            Qrecord[39] = data[40]
            Qrecord[40] = data[41]
            Qrecord[41] = data[42]
            Qrecord[42] = data[43]
            Qrecord[43] = data[44]
            Qrecord[44] = data[45]
            Qrecord[45] = data[46]
            Qrecord[46] = data[47]
            Qrecord[47] = data[48]
            Qrecord[48] = data[49]
            Qrecord[49] = data[50]
            Qrecord[50] = data[51]
            Qrecord[51] = data[52]
            Qrecord[52] = data[53]
            Qrecord[53] = data[54]
            Qrecord[54] = data[55]
            Qrecord[55] = data[56]
            Qrecord[56] = data[57]
            Qrecord[57] = data[58]
            Qrecord[58] = data[59]
            Qrecord[59] = data[60]
            Qrecord[60] = data[61]
            Qrecord[61] = data[62]
            Qrecord[62] = data[63]
            Qrecord[63] = data[64]
            Qrecord[64] = data[65]
            Qrecord[65] = data[66]
            Qrecord[66] = data[67]
            Qrecord[67] = data[68]
            Qrecord[68] = data[69]
            Qrecord[69] = data[70]
            Qrecord[70] = data[71]
            Qrecord[71] = data[72]
            Qrecord[72] = data[73]
            Qrecord[73] = data[74]
            Qrecord[74] = data[75]
            Qrecord[75] = data[76]
            Qrecord[76] = data[77]
            Qrecord[77] = data[78]
            Qrecord[78] = data[79]
            Qrecord[79] = data[80]
            Qrecord[80] = data[81]
            Qrecord[81] = data[82]
            Qrecord[82] = data[83]
            Qrecord[83] = data[84]
            Qrecord[84] = data[85]
            Qrecord[85] = data[86]
            Qrecord[86] = data[87]
            Qrecord[87] = data[88]
            Qrecord[88] = data[89]
            Qrecord[89] = data[90]
            Qrecord[90] = data[91]
            Qrecord[91] = data[92]
            Qrecord[92] = data[93]
            Qrecord[93] = data[94]
            Qrecord[94] = data[95]
            Qrecord[95] = data[96]
            Qrecord[96] = data[97]
            Qrecord[97] = data[98]
            Qrecord[98] = data[99]
            Qrecord[99] = FileName
            Qrecord[100] = GetSeconds(data[25])
            pdd=GetSeconds(data[25])
            if(int(data[36]) > 0):
                Qrecord[101] =  GetOffsetTime(data[1], pdd)
            else:
		        Qrecord[101] =  "0000-00-00 00:00:00"
            durationpdd=pdd+int(data[36])
            Qrecord[102] = GetOffsetTime(data[1], durationpdd)
		

            for i,x in enumerate(Qrecord):
				if x == "":
					Qrecord[i] = None
            Qrecord = tuple(Qrecord)
            value_str = ""
            field_str = ""
            for i,x in enumerate(CDR_TABLE_FIELDS):
				if i != 0:
					value_str += ','
					field_str += ','
       
				value_str += '%s'
				field_str += '`%s`'%(x)
            value_str = "(" + value_str + ")"
            field_str = "(" + field_str + ")"
            
            INSERT_QUERY = "INSERT IGNORE INTO " +TableName+ " " + field_str + " values " + value_str
    #        print INSERT_QUERY
            #cursor.execute (SQLInsert)
            try: 
                #print data
                cursor.execute (INSERT_QUERY,Qrecord)
            except MySQLdb.Warning:
                pass
            #cursor.execute (INSERT_QUERY,data)
            #print cdrvalues[0] + cdrvalues[1],
            #print "Error"
            countins=countins+1                
        inp.close()
        cursor.close()
        # perform a fetch loop using fetchone()
        
        #cursor.execute ("SELECT name, category FROM animal")
        #while (1):
        #    row = cursor.fetchone ()
        #    if row == None:
        #        break
        #    print "%s, %s" % (row[0], row[1])
        #print "Number of rows returned: %d" % cursor.rowcount

        # perform a fetch loop using fetchall()        
    except MySQLdb.Error, e:
        print "Error %d: %s" % (e.args[0], e.args[1])
    conn.commit ()  



# connect to the MySQL server
def ConnectDB():
    global conn
    try:
        conn = MySQLdb.connect (host = "localhost",user = "root",passwd = "",db = "Nextone")
        print "connection created"
		#filterwarnings('ignore',category = MySQLdb.Warning
    except MySQLdb.Error, e:
        print "Error %d: %s" % (e.args[0], e.args[1])
        sys.exit (1)

if __name__ == "__main__":
    parser = optparse.OptionParser("usage: %prog [options]")
    parser.add_option("-b", "--background", dest="background",default=False, action="store_true",
                      help="runs this program in background mode")
    parser.add_option("-t", "--table", dest="tablename", default="CDR",
                      type="string", help="table name to import data, used with -i or -f")
    parser.add_option("-i", "--improt", dest="imp",default=False, action="store_true",
                      help="Imports all the *.ACT files from current dir")
    parser.add_option("-f", "--filename", dest="testfile", default=None,
                      type="string", help="import single file")
    parser.add_option("-d", "--debug", dest="debug", default=False,action="store_true",
                      help="debug mode,does not import data in DB, just prints on screen")
   
 
    (options, args) = parser.parse_args()
    if len(args) > 0:
        print "wrong arguments"
 
    TableName = options.tablename
    Debug = options.debug
    if  options.testfile != None :
        Import = True
        print "Importing file: ",options.testfile
        CDRFolder=""
        ConnectDB()
        ProcessNextoneFile(options.testfile)
        sys.exit(0)

    #conn.close ()
