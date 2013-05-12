/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package gr.pgiotis.BittorrentDigitalPreservation.preservation;

import gr.pgiotis.BittorrentDigitalPreservation.strategies.Strategies;
import java.io.IOException;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.logging.FileHandler;
import java.util.logging.Handler;
import java.util.logging.Level;
import java.util.logging.Logger;
import java.util.logging.SimpleFormatter;

/**
 *
 * @author Panagiotis Giotis <giotis.p@gmail.com>
 */
public class Preservation {

    static Logger LOGGER = Logger.getLogger(Preservation.class.getName());

    /**
     * @param args the command line arguments
     */
    public static void main(String[] args) {
        LOGGER.setLevel(Level.FINE);

        try {
            //connect to DB
            Class.forName("com.mysql.jdbc.Driver");
            String username = "tracker";
            String password = "123";
            String url = "jdbc:mysql://localhost/tracker";
            java.sql.Connection con = DriverManager.getConnection(url, username,
                    password);
            java.sql.Statement st = con.createStatement();

//              DebugLine
//            System.out.println("URL: " + url);
//            System.out.println("Connection: " + con);

            //query= select info_hash,filename,seeders,strategy,startdate from namemap
            ResultSet query = st.executeQuery("select info_hash,filename,"
                    + "threshold,strategy from namemap;");

            ArrayList<String> InfoHashList = new ArrayList<String>();
            ArrayList<String> FilenameList = new ArrayList<String>();
            ArrayList<String> ThresholdList = new ArrayList<String>();
            ArrayList<String> StrategyList = new ArrayList<String>();
            //feed the list with the results
            while (query.next()) {

                InfoHashList.add(query.getString(1));
                FilenameList.add(query.getString(2));
                ThresholdList.add(query.getString(3));
                StrategyList.add(query.getString(4));

            }


//            DebugLine              
//            System.out.println(InfoHashList.toString());
//            System.out.println(FilenameList.toString());
//            System.out.println(ThresholdList.toString());
//            System.out.println(StrategyList.toString());


            // for each torrent file in the DB make 
            // query about the current Seeders count
            for (int i = 0; i < InfoHashList.size(); i++) {

                //query= select count(*) from x+info_hash where status='seeders'
                query = st.executeQuery("select count(*) from x"
                        + InfoHashList.get(i) + " where status='seeder';");
                query.next();
                int seeders = Integer.parseInt(query.getString(1));
                int PreservationThreshold = Integer.parseInt(ThresholdList.get(i));

                //check if seeders < threshold
                if (seeders < PreservationThreshold) {


                    //Call the current Strategy
                    switch (Integer.parseInt(StrategyList.get(i))) {
                        case 1:
                            Strategies.AlertStrategy(InfoHashList.get(i), st);

                            break;
                        case 2:
                            Strategies.EmailStrategy(InfoHashList.get(i), st);
                            break;

                    }

                }
                //initialize logger
                Handler h;
                try {
                    h = new FileHandler("./logFiles/" + InfoHashList.get(i) + ".log", true);
                    SimpleFormatter f = new SimpleFormatter();
                    
                    h.setFormatter(f);
                    LOGGER.addHandler(h);


                    //TODO: save info to log file
                    LOGGER.log(Level.FINE, "=#=#=" + InfoHashList.get(i) + "=#=#=" + PreservationThreshold + "=#=#=" + seeders);
                    LOGGER.removeHandler(h);
                } catch (IOException ex) {
                    Logger.getLogger(Preservation.class.getName()).log(Level.SEVERE, null, ex);
                } catch (SecurityException ex) {
                    Logger.getLogger(Preservation.class.getName()).log(Level.SEVERE, null, ex);
                }




            }



            //Close DB connection
            con.close();

        } catch (ClassNotFoundException ex) {
            Logger.getLogger(Preservation.class.getName()).log(Level.SEVERE, null, ex);
        } catch (SQLException ex) {
            Logger.getLogger(Preservation.class.getName()).log(Level.SEVERE, null, ex);
        }

    }
}
