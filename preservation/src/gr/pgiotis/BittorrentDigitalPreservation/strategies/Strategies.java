/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package gr.pgiotis.BittorrentDigitalPreservation.strategies;

import gr.pgiotis.BittorrentDigitalPreservation.utils.Utils;
import java.util.Date;

/**
 *
 * @author Panagiotis Giotis <giotis.p@gmail.com>
 */
public class Strategies {

    /**
     *
     * @param info_hash
     */
    public static void AlertStrategy(String info_hash) {
        String info = "";
        Date date = new Date();
        info = date.toString() + "  The torrent with hash: " + info_hash + " has "
                + "preservation problem";

        Utils.createLogFile(info, "alert", true);


    }

    /**
     *
     * @param info_hash
     */
    public static void EmailStrategy(String info_hash) {
        String info = "";
        Date date = new Date();
        info = date.toString() + "  The torrent with hash: " + info_hash + " has "
                + "preservation problem";
        Utils.createLogFile(info, "mail", true);


    }

    /**
     *
     * @param info_hash
     */
    public static void SaveStrategy(String info_hash) {
        String info = "";
        Date date = new Date();
        info = date.toString() + "  The torrent with hash: " + info_hash + " has "
                + "preservation problem";
        Utils.createLogFile(info, "save", true);


    }
}
