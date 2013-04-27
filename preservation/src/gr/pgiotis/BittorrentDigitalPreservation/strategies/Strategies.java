/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package gr.pgiotis.BittorrentDigitalPreservation.strategies;

import gr.pgiotis.BittorrentDigitalPreservation.preservation.Preservation;
import gr.pgiotis.BittorrentDigitalPreservation.utils.Utils;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.Date;
import java.util.Properties;
import java.util.logging.Level;
import java.util.logging.Logger;
import javax.mail.Message;
import javax.mail.MessagingException;
import javax.mail.Session;
import javax.mail.Transport;
import javax.mail.internet.InternetAddress;
import javax.mail.internet.MimeMessage;

/**
 *
 * @author Panagiotis Giotis <giotis.p@gmail.com>
 */
public class Strategies {

    /**
     * Implements the alert strategy. Which inform the the alert log file when
     * the the copies for the current torrent is lower than threshold.
     *
     * @param info_hash the hash id for the torrent file which need preservation
     */
    public static void AlertStrategy(String info_hash) {
        String info = "";
        Date date = new Date();
        //create the info for the log file
        info = date.toString() + "  The torrent with hash: " + info_hash + " has "
                + "preservation problem";

        Utils.createLogFile(info, "alert", true);


    }

    /**
     * Implements the e-mail strategy. Which inform the client by mail when the
     * the copies for the current torrent is lower than threshold.
     *
     * @param info_hash the hash id for the torrent file which need preservation
     */
    public static void EmailStrategy(String info_hash,java.sql.Statement st) {
        String torrentName = "";
        String TextMessage = "";
        String to = "bittorrentdigitalpreservation@gmail.com"; // The customers mail

        try {
           
            //query=select filename,infolink from namemap where info_hash='08399a54336d724ba9cfe95cfe709c7347d7ae66';
            ResultSet query = st.executeQuery("select filename,infolink from namemap where info_hash='" + info_hash + "';");
            query.next();
            torrentName=query.getString(1);
            to=query.getString(2);

        }  catch (SQLException ex) {
            Logger.getLogger(Preservation.class.getName()).log(Level.SEVERE, null, ex);
        }
        
        

        Date date = new Date();
        TextMessage = date.toString() + "<br>  The torrent with hash: " + info_hash + " and the name : "+torrentName+" has "
                + "preservation problem";
        Utils.createLogFile(TextMessage, "mail", true);

        //Here starts the mail process

        try {
            //set mail setings 

            String host = "smtp.gmail.com";
            String from = "bittorrentdigitalpreservation@gmail.com";
            String pass = "bdp12345";
            Properties props = System.getProperties();
            props.put("mail.smtp.starttls.enable", "true");
            props.put("mail.smtp.host", host);
            props.put("mail.smtp.user", from);
            props.put("mail.smtp.password", pass);
            props.put("mail.smtp.port", "587");
            props.put("mail.smtp.auth", "true");

            //start the session
            Session session = Session.getDefaultInstance(props, null);
            MimeMessage message = new MimeMessage(session);
            message.setFrom(new InternetAddress(from));

            InternetAddress toAddress = new InternetAddress(to);

            //create the message 
            message.addRecipient(Message.RecipientType.TO, toAddress);

            message.setSubject("Digital Preservation for the " + torrentName + " torrent.");
            message.setText(TextMessage);
            // connect to smtp --> send the mail --> close the connection
            Transport transport = session.getTransport("smtp");
            transport.connect(host, from, pass);
            transport.sendMessage(message, message.getAllRecipients());
            transport.close();

        } catch (MessagingException ex) {
            Logger.getLogger(Strategies.class.getName()).log(Level.SEVERE, null, ex);
        }

    }

    /**
     * Implements the Save to server strategy. Which inform the the Save to
     * server log file when the copies for the current torrent is lower than
     * threshold.
     *
     * @param info_hash the hash id for the torrent file which need preservation
     */
    public static void SaveStrategy(String info_hash,java.sql.Statement st) {
        String info = "";
        String torrentName = "";
        String url="";
        Date date = new Date();
        
        
        try {
           
            //query=select filename,infolink from namemap where info_hash='08399a54336d724ba9cfe95cfe709c7347d7ae66';
            ResultSet query = st.executeQuery("select filename,url from namemap where info_hash='" + info_hash + "';");
            query.next();
            torrentName=query.getString(1);
            url=query.getString(2);

        }  catch (SQLException ex) {
            Logger.getLogger(Preservation.class.getName()).log(Level.SEVERE, null, ex);
        }

        //create info for the log file 
        //=#=#= is the separator 
        info = date.toString() + " =#=#= "+info_hash +" =#=#= "+torrentName +" =#=#= "+url;


        //save to the log file
        Utils.createLogFile(info, "save", true);


    }
}
