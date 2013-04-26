/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package gr.pgiotis.BittorrentDigitalPreservation.preservation;

/**
 *
 * @author panagiotis
 */
public class Preservation {

    /**
     * @param args the command line arguments
     */
    public static void main(String[] args) {
        // TODO code application logic here
        
        System.out.println("preservation starts");
        //connect to DB tracker
        
        //query= select info_hash,filename,seeders,strategy,startdate from namemap
        
        //for each record
            
            //if (nowDate -startdate)>5
                //query= select count(*) from x+info_hash where status='seeders'
                //if query< threshold
                    //call strategy
        
        
        
    }
}
