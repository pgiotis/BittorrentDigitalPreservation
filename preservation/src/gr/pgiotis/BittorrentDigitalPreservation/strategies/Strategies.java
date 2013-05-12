/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package gr.pgiotis.BittorrentDigitalPreservation.strategies;

import gr.pgiotis.BittorrentDigitalPreservation.preservation.Preservation;
import gr.pgiotis.BittorrentDigitalPreservation.utils.Utils;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.Date;
import java.util.logging.Level;
import java.util.logging.Logger;

/**
 *
 * @author Panagiotis Giotis <giotis.p@gmail.com>
 */
public class Strategies {

    private static String logURL = "http://localhost/tracker/FileHistory/logPresenter.php?id=";

    /**
     * Implements the alert strategy. Which inform the the alert log file when
     * the the copies for the current torrent is lower than threshold.
     *
     * @param info_hash the hash id for the torrent file which need preservation
     */
    public static void AlertStrategy(String info_hash, java.sql.Statement st) {
         String torrentName = "";
        String TextMessage = "";
        String to = "bittorrentdigitalpreservation@gmail.com"; // The customers mail
        String url = "";
        try {

            //query=select filename,infolink from namemap where info_hash='08399a54336d724ba9cfe95cfe709c7347d7ae66';
            ResultSet query = st.executeQuery("select filename,url from namemap where info_hash='" + info_hash + "';");
            query.next();
            torrentName = query.getString(1);
            url = query.getString(2);

        } catch (SQLException ex) {
            Logger.getLogger(Preservation.class.getName()).log(Level.SEVERE, null, ex);
        }

        Date date = new Date();
        TextMessage ="<i><u>"+ date.toString() + "</u></i><br>  The torrent with <i>hash:</i> <b>" + info_hash + "</b> <br>and <i>name:</i><b> " + torrentName + "</b><br>has "
                + "preservation problem. <br>You can download .torent file from <a href="+url+">here</a> <br>"
                + " and you can see the file chart <a href="+logURL+info_hash+"> here</a>";

        // send the mail
        Utils.sendMail(TextMessage, to, torrentName);


    }

    /**
     * Implements the e-mail strategy. Which inform the client by mail when the
     * the copies for the current torrent is lower than threshold.
     *
     * @param info_hash the hash id for the torrent file which need preservation
     */
    public static void EmailStrategy(String info_hash, java.sql.Statement st) {
        String torrentName = "";
        String TextMessage = "";
        String to = "bittorrentdigitalpreservation@gmail.com"; // The customers mail
        String url = "";
        try {

            //query=select filename,infolink from namemap where info_hash='08399a54336d724ba9cfe95cfe709c7347d7ae66';
            ResultSet query = st.executeQuery("select filename,infolink,url from namemap where info_hash='" + info_hash + "';");
            query.next();
            torrentName = query.getString(1);
            to = query.getString(2);
            url = query.getString(3);

        } catch (SQLException ex) {
            Logger.getLogger(Preservation.class.getName()).log(Level.SEVERE, null, ex);
        }

        Date date = new Date();
        TextMessage ="<i><u>"+ date.toString() + "</u></i><br>  The torrent with <i>hash:</i> <b>" + info_hash + "</b> <br>and <i>name:</i><b> " + torrentName + "</b><br>has "
                + "preservation problem. <br>You can download .torent file from <a href="+url+">here</a> <br>"
                + " and you can see the file chart <a href="+logURL+info_hash+"> here</a>";

        // send the mail
        Utils.sendMail(TextMessage, to, torrentName);

    }
}
