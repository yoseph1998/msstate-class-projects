///////////////////////////////////////////////////////////////////////////////
// Main Class File:  server.java
// File:             server.java
// Semester:         Data Comm Fall 2019
//
// Authors:           Yoseph Alabddulwahab  &  Lane Wooten
// NetID:            YSA11  &  JLW1291
// Lecturer's Name:  Maxwell Young
//////////////////////////// 80 columns wide //////////////////////////////////

import java.io.*;
import java.net.*;
import java.util.*;

/**
 * A Server class to recieve a file from a client
 * TCP handshakes and UDP file transfers.
 *
 * @author Yoseph Alabdulwahab
 * @author Lane Wooten
 */
public class server {

private static InetAddress ipAddress;
private static String saveFile;
private static int portMin = 1024;
private static int portRange = 64511;
private static int pSizeMin = 4;
private static int pSizeRange = 30;
private static int bufferSize = 30;
private static int receivePort, sendPort;


/**
 *  @param args String array:
 *  @throws IOException
 */
public static void main(String[] args) throws IOException {
        //INSURE INPUT IS VALID
        handleInputs(args);

        //-------------------------
        //--- Data Transfer UDP ---
        //-------------------------
        GoBackN gbn = new GoBackN(ipAddress, sendPort, receivePort);
        String[] logs = gbn.receive();

        //--------------------------
        //--- Write Data to File ---
        //--------------------------
        PrintWriter writer = new PrintWriter(saveFile, "UTF-8");
        writer.print(logs[0]);
        writer.close();

        writer = new PrintWriter("arrival.log", "UTF-8");
        writer.print(logs[1]);
        writer.close();
}

/**
 * Makes sure that input follows specifications or will print an error.
 * @param args String array of size 4 <emulator address> <receive port> <send port> <filename>
 */
public static void handleInputs(String[] args) {
        if (args.length != 4) {
                System.err.println("Usage: java Server <emulator address> <receive port> <send port> <filename>");
                return;
        }
        try {
                ipAddress = InetAddress.getByName(args[0]);
                receivePort = Integer.parseInt(args[1]);
                sendPort = Integer.parseInt(args[2]);
                saveFile = args[3];
        } catch (Exception e) {
                System.err.println("Usage: java Server <emulator address> <receive port> <send port> <filename>");
        }
}
}
