/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package gr.pgiotis.BittorrentDigitalPreservation.utils;

import java.io.FileWriter;
import java.io.IOException;
import java.io.PrintWriter;

/**
 * 
 * @author Panagiotis Giotis <giotis.p@gmail.com>
 */
public class Utils {
    
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
