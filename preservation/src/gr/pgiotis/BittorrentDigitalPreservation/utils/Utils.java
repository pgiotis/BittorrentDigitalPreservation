/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package gr.pgiotis.BittorrentDigitalPreservation.utils;

import gr.pgiotis.BittorrentDigitalPreservation.strategies.Strategies;
import java.io.FileWriter;
import java.io.IOException;
import java.io.PrintWriter;
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
public class Utils {
    
    
    public static void sendMail(String TextMessage,String to,String torrentName){
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
//            message.setText(TextMessage);
            message.setContent(TextMessage,"text/html" );
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
     * Create the log files
     *
     * @param text the text that i want to write in the file
     * @param appendToFile boolean parameter for the append
     */
    public static void createLogFile(String text,String logName ,boolean appendToFile) {

        PrintWriter pw = null;

        try {

            if (appendToFile) {

                pw = new PrintWriter(new FileWriter("./logFiles/"+logName+".log", true));  //If the file already exists, start writing at the end of it.

            } else {

                pw = new PrintWriter(new FileWriter("./logFiles/"+logName+".log"));

            }

            pw.println(text);                                          // write to output.out
            pw.flush();

        } catch (IOException e) {
            e.printStackTrace();
        } finally {
            //Close the PrintWriter
            if (pw != null) {
                pw.close();
            }

        }

    }
    
    
    
    
    
    
}
